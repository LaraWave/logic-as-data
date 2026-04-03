<?php

namespace LaraWave\LogicAsData\Services;

use LaraWave\LogicAsData\Enums\ClauseStatus;

class TraceBuilder
{
    private array $predicate;
    private array $finding = [];
    private array $children = [];

    public function __construct(array $predicate)
    {
        $this->predicate = $predicate;
    }

    public function addChild(TraceBuilder $childBuilder): void
    {
        $this->children[] = $childBuilder;
    }

    public function capture(ClauseStatus $status, float $duration, array $extra = []): void
    {
        $this->finding = array_merge([
            'status'   => $status->value,
            'duration' => $duration,
        ], $extra);
    }

    public function toArray(): array
    {
        $result = $this->predicate;

        if (! empty($this->children)) {
            $result['clauses'] = array_map(fn ($child) => $child->toArray(), $this->children);
        }

        $result['_finding'] = empty($this->finding) ? (object)[] : $this->finding;

        return $result;
    }
}
