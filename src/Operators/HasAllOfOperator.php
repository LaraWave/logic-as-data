<?php

namespace LaraWave\LogicAsData\Operators;

use Illuminate\Support\Arr;

class HasAllOfOperator extends Operator
{
    public function check(mixed $sourceValue, mixed $targetValue = null): bool
    {
        return empty(array_diff(Arr::wrap($targetValue), Arr::wrap($sourceValue)));
    }
}
