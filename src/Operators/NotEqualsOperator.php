<?php

namespace LaraWave\LogicAsData\Operators;

class NotEqualsOperator extends Operator
{
    public function check(mixed $sourceValue, mixed $targetValue = null): bool
    {
        return $sourceValue != $targetValue;
    }
}
