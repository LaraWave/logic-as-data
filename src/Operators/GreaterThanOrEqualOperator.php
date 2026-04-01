<?php

namespace LaraWave\LogicAsData\Operators;

class GreaterThanOrEqualOperator extends Operator
{
    public function check(mixed $sourceValue, mixed $targetValue = null): bool
    {
        return $sourceValue >= $targetValue;
    }
}
