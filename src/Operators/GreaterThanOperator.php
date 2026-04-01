<?php

namespace LaraWave\LogicAsData\Operators;

class GreaterThanOperator extends Operator
{
    public function check(mixed $sourceValue, mixed $targetValue = null): bool
    {
        return $sourceValue > $targetValue;
    }
}
