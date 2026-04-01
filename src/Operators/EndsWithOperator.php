<?php

namespace LaraWave\LogicAsData\Operators;

class EndsWithOperator extends Operator
{
    public function check(mixed $sourceValue, mixed $targetValue = null): bool
    {
        return str_ends_with((string) $sourceValue, (string) $targetValue);
    }
}
