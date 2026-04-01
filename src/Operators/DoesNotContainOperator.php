<?php

namespace LaraWave\LogicAsData\Operators;

use Illuminate\Support\Arr;

class DoesNotContainOperator extends Operator
{
    public function check(mixed $sourceValue, mixed $targetValue = null): bool
    {
        // If the source value is an array
        if (is_array($sourceValue)) {
            $targetArray = Arr::wrap($targetValue);
            return empty(array_intersect($targetArray, $sourceValue));
        }

        // If it's a string
        return !str_contains((string) $sourceValue, (string) $targetValue);
    }
}
