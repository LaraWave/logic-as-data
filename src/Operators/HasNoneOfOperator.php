<?php

namespace LaraWave\LogicAsData\Operators;

use Illuminate\Support\Arr;

class HasNoneOfOperator extends Operator
{
    public function check(mixed $sourceValue, mixed $targetValue = null): bool
    {
        return empty(array_intersect(Arr::wrap($targetValue), Arr::wrap($sourceValue)));
    }
}
