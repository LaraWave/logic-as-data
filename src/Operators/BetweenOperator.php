<?php

namespace LaraWave\LogicAsData\Operators;

use Illuminate\Support\Arr;

class BetweenOperator extends Operator
{
    public function check(mixed $sourceValue, mixed $targetValue = null): bool
    {
        $targetArray = Arr::wrap($targetValue);

        if (count($targetArray) !== 2) {
            return false; // Malformed rule
        }

        return $sourceValue >= $targetArray[0] && $sourceValue <= $targetArray[1];
    }
}
