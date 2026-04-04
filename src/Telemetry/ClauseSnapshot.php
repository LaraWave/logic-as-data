<?php

namespace LaraWave\LogicAsData\Telemetry;

use LaraWave\LogicAsData\Enums\ClauseStatus;

class ClauseSnapshot
{
    private array $predicate;
    private array $finding = [];
    private array $children = [];

    public function __construct(array $predicate)
    {
        $this->predicate = $predicate;
    }

    public function addChild(self $childSnapshot): void
    {
        $this->children[] = $childSnapshot;
    }

    public function capture(
        ClauseStatus $status,
        float $duration,
        array $extra = []
    ): void {
        $metrics = ['status' => $status->value];
        if ($status !== ClauseStatus::SKIPPED) {
            $metrics['duration'] = $duration;
        }
        $this->finding = array_merge($metrics, $extra);
    }

    public function toArray(): array
    {
        $result = $this->predicate;

        if (! empty($this->children)) {
            $result['clauses'] = array_map(fn (self $child) => $child->toArray(), $this->children);
        }

        $result['_finding'] = empty($this->finding) ? (object)[] : $this->finding;

        return $result;
    }
}
