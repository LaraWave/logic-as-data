<?php

namespace LaraWave\LogicAsData\Operators;

class IsEmptyOperator extends Operator
{
    public function check(mixed $sourceValue, mixed $targetValue = null): bool
    {
        return empty($sourceValue);
    }
}
