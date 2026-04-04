<?php

namespace LaraWave\LogicAsData;

use LaraWave\LogicAsData\Telemetry\TelemetryRecorder;
use LaraWave\LogicAsData\Telemetry\ClauseSnapshot;
use LaraWave\LogicAsData\Enums\EvaluationStrategy;
use Symfony\Component\HttpFoundation\Response;
use LaraWave\LogicAsData\Telemetry\RuleTrace;
use LaraWave\LogicAsData\Enums\RuleStatus;
use LaraWave\LogicAsData\Models\LogicRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Throwable;
class LogicEngine
{
    public function __construct(
        private LogicRegistry $registry
    ) {}

    /**
     * EVALUATION ONLY: Determine if rules for a hook satisfy the context.
     * Evaluates the logic tree and generates telemetry records,
     * but DOES NOT execute actions.
     */
    public function passes(
        string $hook,
        array $context = [],
        array|Model|null $subjects = null,
        EvaluationStrategy $strategy = EvaluationStrategy::ANY
    ): bool {
        $rules = $this->getRulesForHook($hook);

        if ($rules->isEmpty()) {
            return false;
        }

        $evaluator = $this->registry->evaluator('default');

        $subjectsArray = $subjects instanceof Model ? [$subjects] : ($subjects ?? []);

        $recorder = new TelemetryRecorder($hook, $context, $subjectsArray);
        
        $finalResult = $strategy->isAll();
        $thrownException = null;

        foreach ($rules as $rule) {
            $predicate = $rule->definition['predicate'] ?? [];

            $trace = new RuleTrace($rule);
            $clauseSnapshot = new ClauseSnapshot($predicate);
            $rulePassed = false;

            try {
                $rulePassed = $evaluator->evaluate($predicate, $context, $clauseSnapshot);

                if ($rulePassed) {
                    $trace->markPassed();
                }
            } catch (Throwable $e) {
                $trace->markError($e);
                $thrownException = $e;
            }

            $recorder->add($trace, $clauseSnapshot);

            if ($thrownException) {
                break;
            }

            if ($strategy->isAll() && !$rulePassed) {
                $finalResult = false;
                break;
            }

            if ($strategy->isAny() && $rulePassed) {
                $finalResult = true;
                break;
            }
        }

        $recorder->record();

        if ($thrownException) {
            throw $thrownException;
        }

        return $finalResult;
    }

    /**
     * Evaluates conditions and fires assigned actions if any.
     */
    public function trigger(
        string $hook,
        array $context = [],
        array|Model|null $subjects = null,
    ): mixed {
        $rules = $this->getRulesForHook($hook);
        $result = null;

        if ($rules->isEmpty()) {
            return null;
        }

        $evaluator = $this->registry->evaluator('default');
        $subjectsArray = $subjects instanceof Model ? [$subjects] : ($subjects ?? []);
        $recorder = new TelemetryRecorder($hook, $context, $subjectsArray);
        $thrownException = null;

        foreach ($rules as $rule) {
            $predicate = $rule->definition['predicate'] ?? [];

            $trace = new RuleTrace($rule);
            $clauseSnapshot = new ClauseSnapshot($predicate);

            try {
                if ($evaluator->evaluate($predicate, $context, $clauseSnapshot)) {
                    $trace->markPassed();
                    $actions = $rule->definition['actions'] ?? [];
                    $trace->registerActions($actions);
                    $result = $this->executeActions($actions, $context, $trace);
                }
            } catch (Throwable $e) {
                $trace->markError($e);
                $thrownException = $e;
            }

            $recorder->add($trace, $clauseSnapshot);

            if ($thrownException || ($result instanceof Response)) {
                break;
            }
        }

        $recorder->record();

        if ($thrownException) {
            throw $thrownException;
        }

        if ($result instanceof Response) {
            return $result;
        }
        return null;;
    }

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

    public function clearCacheForHook(string $hook): void
    {
        $key = config('logic-as-data.cache.key', 'logic_rules') . ':hook:' . $hook;

        Cache::forget($key);
    }

    protected function fetchRules(string $hook): Collection
    {
        return LogicRule::query()
            ->where('hook', $hook)
            ->where('status', RuleStatus::ACTIVE)
            ->orderByDesc('priority')
            ->get();
    }

    protected function executeActions(
        array $actions,
        array $context,
        RuleTrace $trace
    ): mixed {
        $result = null;
        foreach ($actions as $index => $action) {
            $alias = $action['alias'] ?? null;

            if (! $alias) {
                throw new InvalidArgumentException('LogicAsData Error: Action definition is missing alias.');
            }

            $params = $action['params'] ?? [];

            $startTime = microtime(true);
            $thrownError = null;

            try {
                $result = $this->registry->action($alias)->execute($alias, $params, $context);
            } catch (Throwable $e) {
                $thrownError = $e->getMessage();
                throw $e;
            } finally {
                $duration = (microtime(true) - $startTime) * 1000;
                if ($actionSnapshot = $trace->getActionSnapshot($index)) {
                    if ($thrownError) {
                        $actionSnapshot->markFailed($duration, $thrownError);
                    } else {
                        $actionSnapshot->markSuccess($duration);
                    }
                }
            }

            if ($result instanceof Response) {
                break;
            }
        }

        return $result;
    }
}
