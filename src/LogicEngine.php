<?php

namespace LaraWave\LogicAsData;

use LaraWave\LogicAsData\Models\LogicRule;
use LaraWave\LogicAsData\Enums\RuleStatus;
use Illuminate\Support\Facades\Cache;
use LaraWave\LogicAsData\LogicRegistry;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;

class LogicEngine
{
    public function __construct(private LogicRegistry $registry) {}

    /**
     * EVALUATION ONLY: Determine if any active rules for a hook satisfy the context.
     * This DOES NOT execute actions. It's useful for gating logic (e.g., if(passes)).
     *
     * @param string $hook The feature alias or hook name.
     * @param array $context The runtime state.
     * @param string $strategy How to evaluate multiple rules: 'any' (OR) or 'all' (AND).
     * @return bool
     */
    public function passes(string $hook, array $context = [], string $strategy = 'any'): bool
    {
        $rules = $this->getRulesForHook($hook);

        // If no rules exist for this feature, it's inactive.
        if ($rules->isEmpty()) {
            return false;
        }

        $evaluator = $this->registry->evaluator('default');

        if ($strategy === 'all') {
            // ---------------------------------------------------------
            // THE "ALL" STRATEGY (AND)
            // Every single rule must evaluate to true.
            // ---------------------------------------------------------
            foreach ($rules as $rule) {
                $predicate = $rule->definition['predicate'] ?? [];
                if (! $evaluator->evaluate($predicate, $context)) {
                    return false; // One rule failed, so the 'all' condition fails immediately.
                }
            }
            return true; // all rules passed
        }

        // ---------------------------------------------------------
        // THE "ANY" STRATEGY (OR) - Default
        // At least any one rule needs to evaluate to true.
        // ---------------------------------------------------------
        foreach ($rules as $rule) {
            $predicate = $rule->definition['predicate'] ?? [];
            if ($evaluator->evaluate($predicate, $context)) {
                // If even one rule passes, stop checking and return true immediately
                return true;
            }
        }

        return false; // nothing passed.
    }

    /**
     * EVALUATE AND EXECUTE: The main entry point. 
     * Finds rules for a hook, checks conditions, and fires their actions if passed.
     *
     * @param string $hook
     * @param array $context
     * @return void
     */
    public function trigger(string $hook, array $context = []): void
    {
        $rules = $this->getRulesForHook($hook);
        $evaluator = $this->registry->evaluator('default');

        foreach ($rules as $rule) {
            $predicate = $rule->definition['predicate'] ?? [];
            if ($evaluator->evaluate($predicate, $context)) {
                $actions = $rule->definition['actions'] ?? [];
                $this->executeActions($actions, $context);
            }
        }
    }

    /**
     * Retrieve all rules for a hook, respecting the caching layer.
     *
     * @param string $hook
     * @return \Illuminate\Support\Collection<int, LogicRule>
     */
    public function getRulesForHook(string $hook): Collection
    {
        $cacheConfig = config('logic-as-data.cache');

        if (! config('logic-as-data.cache.enabled')) {
            return $this->fetchRules($hook);
        }

        return Cache::remember(
            config('logic-as-data.cache.key') . ':hook:' . $hook,
            config('logic-as-data.cache.ttl'),
            fn () => $this->fetchRules($hook)
        );
    }

    /**
     * Perform the actual database query for active rules.
     */
    protected function fetchRules(string $hook): Collection
    {
        return LogicRule::query()
            ->where('hook', $hook)
            ->where('status', RuleStatus::ACTIVE)
            ->orderByDesc('priority')
            ->get();
    }

    /**
     * Safely executes a single action
     *
     * @param array $actionConfig The individual action from the JSON.
     * @param array $context The data payload.
     */
    protected function executeActions(array $actions, array $context): void
    {
        foreach ($actions as $action) {
            $alias = $action['alias'] ?? null;

            if (! $alias) {
                throw new InvalidArgumentException('LogicAsData Error: Action definition is missing alias.');
            }

            $params = $action['params'] ?? [];

            $this->registry->action($alias)->execute($alias, $params, $context);
        }
    }
}
