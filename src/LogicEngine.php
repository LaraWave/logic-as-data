<?php

namespace LaraWave\LogicAsData;

use LaraWave\LogicAsData\Services\TelemetryRecorder;
use LaraWave\LogicAsData\Enums\EvaluationStrategy;
use LaraWave\LogicAsData\Services\TraceBuilder;
use LaraWave\LogicAsData\Enums\TraceStatus;
use LaraWave\LogicAsData\Enums\RuleStatus;
use LaraWave\LogicAsData\Models\LogicRule;
use LaraWave\LogicAsData\LogicRegistry;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Throwable;

class LogicEngine
{
    public function __construct(
        private LogicRegistry $registry,
        private TelemetryRecorder $recorder
    ) {}

    /**
     * EVALUATION ONLY: Determine if rules for a hook satisfy the context.
     * Evaluates the logic tree and generates telemetry record,
     * but DOES NOT execute actions.
     */
    public function passes(
        string $hook,
        array $context = [],
        EvaluationStrategy $strategy = EvaluationStrategy::ANY
    ): bool {
        $rules = $this->getRulesForHook($hook);

        if ($rules->isEmpty()) {
            return false;
        }

        $evaluator = $this->registry->evaluator('default');

        $traces = [];
        $overallStartTime = microtime(true);
        $finalResult = $strategy->isAll();

        foreach ($rules as $rule) {
            $startTime = microtime(true);
            $status = TraceStatus::FAILED;
            $thrownException = null;

            $predicate = $rule->definition['predicate'] ?? [];
            $traceBuilder = new TraceBuilder($predicate);

            try {
                if ($evaluator->evaluate($predicate, $context, $traceBuilder)) {
                    $status = TraceStatus::PASSED;
                }
            } catch (Throwable $e) {
                $status = TraceStatus::ERROR;
                $thrownException = $e;
            }

            $traces[] = [
                'logic_rule_id' => $rule->id,
                'status'        => $status->value,
                'error'         => $thrownException ? $thrownException->getMessage() : null,
                'duration'      => round((microtime(true) - $startTime) * 1000, 2),
                'snapshots'     => [
                    'predicate' => $traceBuilder->toArray(),
                    'actions'   => null
                ],
            ];

            if ($thrownException) {
                break;
            }

            if ($strategy->isAll() && $status === TraceStatus::FAILED) {
                $finalResult = false;
                break;
            }

            if ($strategy->isAny() && $status === TraceStatus::PASSED) {
                $finalResult = true;
                break;
            }
        }

        $totalDuration = round((microtime(true) - $overallStartTime) * 1000, 2);
        $this->recorder->record($hook, $context, $traces, $totalDuration);

        if ($thrownException) {
            throw $thrownException;
        }

        return $finalResult;
    }

    /**
     * Evaluates conditions and fires assigned actions if any.
     *
     * @param string $hook
     * @param array $context
     * @return void
     */
    public function trigger(string $hook, array $context = []): void
    {
        $rules = $this->getRulesForHook($hook);

        if ($rules->isEmpty()) {
            return;
        }

        $evaluator = $this->registry->evaluator('default');

        $traces = [];
        $overallStartTime = microtime(true);

        foreach ($rules as $rule) {
            $startTime = microtime(true);
            $status = TraceStatus::FAILED;
            $thrownException = null;
            $actions = null;

            $predicate = $rule->definition['predicate'] ?? [];
            $traceBuilder = new TraceBuilder($predicate);

            try {
                if ($evaluator->evaluate($predicate, $context, $traceBuilder)) {
                    $status = TraceStatus::PASSED;
                    $actions = $rule->definition['actions'] ?? [];

                    $this->executeActions($actions, $context);
                }
            } catch (Throwable $e) {
                $status = TraceStatus::ERROR;
                $thrownException = $e;
            }

            $traces[] = [
                'logic_rule_id' => $rule->id,
                'status'        => $status->value,
                'error'         => $thrownException ? $thrownException->getMessage() : null,
                'duration'      => round((microtime(true) - $startTime) * 1000, 2),
                'snapshots'     => [
                    'predicate' => $traceBuilder->toArray(), 
                    'actions'   => $actions
                ],
            ];

            if ($thrownException) {
                break;
            }
        }

        $totalDuration = round((microtime(true) - $overallStartTime) * 1000, 2);
        $this->recorder->record($hook, $context, $traces, $totalDuration);

        if ($thrownException) {
            throw $thrownException;
        }
    }

    /**
     * Retrieve all active rules for a hook
     *
     * @param string $hook
     * @return \Illuminate\Support\Collection
     */
    public function getRulesForHook(string $hook): Collection
    {
        if (! config('logic-as-data.cache.enabled', true)) {
            return $this->fetchRules($hook);
        }

        return Cache::remember(
            config('logic-as-data.cache.key', 'logic_rules') . ':hook:' . $hook,
            config('logic-as-data.cache.ttl', 3600),
            fn () => $this->fetchRules($hook)
        );
    }

    protected function fetchRules(string $hook): Collection
    {
        return LogicRule::query()
            ->where('hook', $hook)
            ->where('status', RuleStatus::ACTIVE)
            ->orderByDesc('priority')
            ->get();
    }

    /**
     * Executes configured actions sequentially.
     *
     * @param array $actions Array of actions from the JSON definition.
     * @param array $context The data payload.
     * @throws InvalidArgumentException
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
