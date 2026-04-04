<?php

namespace LaraWave\LogicAsData\Telemetry;

use LaraWave\LogicAsData\Telemetry\ActionSnapshot;
use LaraWave\LogicAsData\Enums\TraceStatus;
use Throwable;

class RuleTrace
{
    private mixed $rule;
    private float $startTime;
    private float $logicDuration = 0.0;
    private TraceStatus $status = TraceStatus::FAILED;
    private ?Throwable $exception = null;
    private array $actions = [];

    public function __construct(mixed $rule)
    {
        $this->rule = $rule;
        $this->startTime = microtime(true);
    }

    public function markPassed(): void
    {
        $this->status = TraceStatus::PASSED;
        $this->logicDuration = (microtime(true) - $this->startTime) * 1000;
    }

    public function markError(Throwable $e): void
    {
        $this->status = TraceStatus::ERROR;
        $this->exception = $e;
        $this->logicDuration = (microtime(true) - $this->startTime) * 1000;
    }

    public function hasError(): bool
    {
        return $this->status === TraceStatus::ERROR;
    }

    public function registerActions(array $actions): void
    {
        $this->actions = array_map(function ($action) {
            return new ActionSnapshot($action['alias'] ?? '', $action['params'] ?? []);
        }, $actions);
    }

    public function getActionSnapshot(int $index): ?ActionSnapshot
    {
        return $this->actions[$index] ?? null;
    }

    public function toRecordArray(ClauseSnapshot $clauseSnapshot): array
    {
        if ($this->logicDuration === 0.0) {
            $this->logicDuration = (microtime(true) - $this->startTime) * 1000;
        }

        return [
            'logic_rule_id' => $this->rule->id ?? null,
            'status'        => $this->status->value,
            'error'         => $this->exception ? $this->exception->getMessage() : null,
            'duration'      => round($this->logicDuration, 2),
            'snapshot'      => [
                'predicate' => $clauseSnapshot->toArray(),
                'actions'   => array_map(fn ($action) => $action->toArray(), $this->actions), 
            ],
        ];
    }
}
