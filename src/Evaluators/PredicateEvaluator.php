<?php

namespace LaraWave\LogicAsData\Evaluators;

use LaraWave\LogicAsData\Telemetry\ClauseSnapshot;
use LaraWave\LogicAsData\Enums\ClauseStatus;
use InvalidArgumentException;
use Throwable;

/**
 * Evaluates the recursive "predicate" tree of Logic-as-Data JSON definition.
 */
class PredicateEvaluator extends Evaluator
{
    /**
     * Entry point for the evaluation process.
     */
    public function evaluate(
        array $predicate,
        array $context,
        ?ClauseSnapshot $snapshot = null
    ): bool {
        $snapshot = $snapshot ?? new ClauseSnapshot($predicate);

        return $this->evaluateNode($predicate, $context, $snapshot);
    }

    /**
     * Routes the current node to either group logic (AND/OR) or a single concrete clause.
     */
    private function evaluateNode(
        array $node,
        array $context,
        ClauseSnapshot $snapshot
    ): bool {
        if (isset($node['clauses']) || isset($node['combinator'])) {
            return $this->evaluateGroup($node, $context, $snapshot);
        }

        return $this->evaluateSingle($node, $context, $snapshot);
    }

    /**
     * Evaluates a collection of clauses recursively.
     */
    private function evaluateGroup(
        array $group,
        array $context,
        ClauseSnapshot $snapshot
    ): bool {
        $startTime = microtime(true);
        $combinator = strtolower($group['combinator'] ?? 'and');
        $clauses = $group['clauses'] ?? [];
        
        $shortCircuited = false;
        $status = ClauseStatus::FAILED;

        if (empty($clauses)) {
            $snapshot->capture(
                ClauseStatus::PASSED,
                round((microtime(true) - $startTime) * 1000, 2)
            );
            return true;
        }

        try {
            foreach ($clauses as $clause) {
                $childSnapshot = new ClauseSnapshot($clause);
                $snapshot->addChild($childSnapshot);

                if ($shortCircuited) {
                    $childSnapshot->capture(ClauseStatus::SKIPPED, 0);
                    continue;
                }

                $result = $this->evaluateNode($clause, $context, $childSnapshot);

                if ($combinator === 'and' && !$result) {
                    $shortCircuited = true;
                }

                if ($combinator === 'or' && $result) {
                    $shortCircuited = true;
                }
            }

            $passed = ($combinator === 'and') ? !$shortCircuited : $shortCircuited;
            $status = $passed ? ClauseStatus::PASSED : ClauseStatus::FAILED;

            return $passed;

        } catch (Throwable $e) {
            $status = ClauseStatus::ERROR;
            throw $e;
        } finally {
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            $snapshot->capture($status, $duration);
        }
    }

    /**
     * Extracts the real value from the context and compares it to the target.
     */
    private function evaluateSingle(
        array $clause,
        array $context,
        ClauseSnapshot $snapshot
    ): bool {
        $startTime = microtime(true);
        $status = ClauseStatus::FAILED;
        $analytics = [];

        try {
            $operatorKey = $clause['operator'] ?? null;

            if (! $operatorKey) {
                throw new InvalidArgumentException('Logic Engine: Each clause must contain an "operator".');
            }

            $sourceValue = $this->resolveSource($clause['source'] ?? [], $context);
            $targetValue = $this->resolveTarget($clause['target'] ?? [], $context);

            $analytics['resolved_source'] = $sourceValue;
            $analytics['resolved_target'] = $targetValue;

            $passed = $this->registry
                ->operator($operatorKey)
                ->check($sourceValue, $targetValue);
                
            $status = $passed ? ClauseStatus::PASSED : ClauseStatus::FAILED;

            return $passed;
        } catch (Throwable $e) {
            $status = ClauseStatus::ERROR;
            $analytics['error'] = $e->getMessage();
            throw $e;
        } finally {
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            $snapshot->capture($status, $duration, $analytics);
        }
    }

    /**
     * Resolves the left-hand side of the logical operator.
     */
    private function resolveSource(array $source, array $context): mixed
    {
        if (empty($source) || !isset($source['alias'])) {
            throw new InvalidArgumentException('Logic Engine: Source definition is missing or lacks an alias.');
        }

        $extractor = $this->registry->extractor($source['alias']);

        $localContext = array_merge($context, [
            '_params' => $source['params'] ?? []
        ]);

        return $extractor->extract($source['alias'], $localContext);
    }

    /**
     * Resolves the right-hand side of the logical operator.
     */
    protected function resolveTarget(array $target, array $context): mixed
    {
        if (empty($target) || !isset($target['alias'])) {
            throw new InvalidArgumentException('Logic Engine: Target definition is missing or lacks an alias.');
        }

        $resolver = $this->registry->resolver($target['alias']);

        return $resolver->resolve($target['alias'], $context, $target['params'] ?? []);
    }
}
