<?php

namespace LaraWave\LogicAsData\Evaluators;

use InvalidArgumentException;

/**
 * Evaluates the recursive "predicate" tree of your Logic-as-Data JSON.
 */
class PredicateEvaluator extends Evaluator
{
    /**
     * Entry point for the evaluation process.
     */
    public function evaluate(array $predicate, array $context): bool
    {
        return $this->evaluateNode($predicate, $context);
    }

    /**
     * Routes the current node to either group logic (AND/OR) or a single clause.
     */
    private function evaluateNode(array $node, array $context): bool
    {
        // If 'clauses' or 'combinator' exists, this is a logical group node.
        if (isset($node['clauses']) || isset($node['combinator'])) {
            return $this->evaluateGroup($node, $context);
        }

        // Otherwise, it is a concrete single rule.
        return $this->evaluateSingle($node, $context);
    }

    /**
     * Evaluates a collection of clauses with short-circuiting performance.
     */
    private function evaluateGroup(array $group, array $context): bool
    {
        // Default to 'and' if no combinator is explicitly provided
        $combinator = strtolower($group['combinator'] ?? 'and');
        $clauses = $group['clauses'] ?? [];


        // An empty group passes by default to prevent blocking
        if (empty($clauses)) {
            return true;
        }

        foreach ($clauses as $clause) {
            $result = $this->evaluateNode($clause, $context);

            // If combinator is 'and', and one fails, the whole group fails
            if ($combinator === 'and' && ! $result) {
                return false;
            }

            // If combinator is 'or', and one passes, the whole group passes
            if ($combinator === 'or' && $result) {
                return true;
            }
        }

        // 'and' returns true (because nothing failed).
        // 'or' returns false (because nothing passed).
        return $combinator === 'and';
    }

    /**
     * Extracts the real value from the data context and compares it to the target.
     */
    private function evaluateSingle(array $clause, array $context): bool
    {
        $operatorKey = $clause['operator'] ?? null;
        if (! $operatorKey) {
            // dd($clause, $context);
            throw new InvalidArgumentException("Logic Engine: Each clause must contain an 'operator'.");
        }

        // Fetch the source value
        $sourceValue = $this->resolveSource($clause['source'] ?? null, $context);

        $targetValue = $this->resolveTarget($clause['target'] ?? null, $context);

        // Check if the source and target values match using the Operator
        $passed = $this->registry->operator($operatorKey)->check($sourceValue, $targetValue);
        return $passed;
    }

    private function resolveSource(mixed $source, array $context): mixed
    {
        if (empty($source)) {
            throw new InvalidArgumentException("Logic Engine: missing source in JSON.");
        }

        if (is_array($source)) {
            if (! isset($source['alias'])) {
                throw new InvalidArgumentException("Logic Engine: missing source alias in JSON.");
            }

            $extractor = $this->registry->extractor($source['alias']);

            // Merge params into context for the extractor to use
            $localContext = array_merge($context, [
                '_params' => $source['params'] ?? []
            ]);

            return $extractor->extract($source['alias'], $localContext);
        }

        // Fallback for simple string aliases (e.g., "user.id")
        return $this->registry->extractor($source)->extract($source, $context);
    }

    protected function resolveTarget(mixed $target, array $context): mixed
    {
        if (is_array($target) && isset($target['alias'])) {
            $resolver = $this->registry->resolver($target['alias']);

            // Pass the params explicitly to the resolver
            return $resolver->resolve($target['alias'], $context, $target['params'] ?? []);
        }

        // It's a static value (string, int, bool, array etc)
        return $target;
    }
}
