<?php

namespace LaraWave\LogicAsData\Telemetry;

use LaraWave\LogicAsData\Enums\TraceStatus;
use Throwable;

class RuleTrace
{
    private mixed $rule;
    private float $startTime;
    private TraceStatus $status = TraceStatus::FAILED;
    private ?Throwable $exception = null;
    private array $actions = [];

    public function __construct(mixed $rule)
    {
        $this->rule = $rule;
        $this->startTime = microtime(true);
    }

    public function markPassed(array $actions = []): void
    {
        $this->status = TraceStatus::PASSED;
        $this->actions = $actions;
    }

    public function markError(Throwable $e): void
    {
        $this->status = TraceStatus::ERROR;
        $this->exception = $e;
    }

    public function hasError(): bool
    {
        return $this->status === TraceStatus::ERROR;
    }

    public function toRecordArray(ClauseSnapshot $snapshot): array
    {
        return [
            'logic_rule_id' => $this->rule->id ?? null,
            'status'        => $this->status->value,
            'error'         => $this->exception ? $this->exception->getMessage() : null,
            'duration'      => round((microtime(true) - $this->startTime) * 1000, 2),
            'snapshot'      => [
                'predicate' => $snapshot->toArray(),
                'actions'   => $this->actions,
            ],
        ];
    }
}
