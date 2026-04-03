<?php

namespace LaraWave\LogicAsData\Evaluators;

use LaraWave\LogicAsData\Services\TraceBuilder;
use LaraWave\LogicAsData\LogicRegistry;

abstract class Evaluator
{
    public function __construct(protected LogicRegistry $registry) {}

    /**
     * Evaluate the given logic block against the provided data context.
     *
     * @param array $rules The logic definition (e.g., the 'predicate' array).
     * @param array $data  The runtime data context (e.g., user, cart).
     * @return bool
     */
    abstract public function evaluate(
        array $predicate,
        array $context,
        ?TraceBuilder $traceBuilder = null
    ): bool;
}
