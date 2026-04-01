<?php

namespace LaraWave\LogicAsData\Operators;

class StartsWithOperator extends Operator
{
    public function check(mixed $sourceValue, mixed $targetValue = null): bool
    {
        return str_starts_with((string) $sourceValue, (string) $targetValue);
    }
}
