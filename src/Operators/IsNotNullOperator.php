<?php

namespace LaraWave\LogicAsData\Operators;

class IsNotNullOperator extends Operator
{
    public function check(mixed $sourceValue, mixed $targetValue = null): bool
    {
        return !is_null($sourceValue);
    }
}
