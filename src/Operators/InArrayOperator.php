<?php

namespace LaraWave\LogicAsData\Operators;

use Illuminate\Support\Arr;

class InArrayOperator extends Operator
{
    public function check(mixed $sourceValue, mixed $targetValue = null): bool
    {
        return in_array($sourceValue, Arr::wrap($targetValue));
    }
}
