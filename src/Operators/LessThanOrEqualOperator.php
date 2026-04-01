<?php

namespace LaraWave\LogicAsData\Operators;

class LessThanOrEqualOperator extends Operator
{
    public function check(mixed $sourceValue, mixed $targetValue = null): bool
    {
        return $sourceValue <= $targetValue;
    }
}
