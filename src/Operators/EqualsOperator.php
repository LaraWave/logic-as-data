<?php

namespace LaraWave\LogicAsData\Operators;

class EqualsOperator extends Operator
{
    public function check(mixed $sourceValue, mixed $targetValue = null): bool
    {
        return $sourceValue == $targetValue; 
    }
}
