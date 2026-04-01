<?php

namespace LaraWave\LogicAsData\Operators;

class IsNotEmptyOperator extends Operator
{
    public function check(mixed $sourceValue, mixed $targetValue = null): bool
    {
        return !empty($sourceValue);
    }
}
