<?php

namespace LaraWave\LogicAsData\Operators;

class MatchesRegexOperator extends Operator
{
    public function check(mixed $sourceValue, mixed $targetValue = null): bool
    {
        return preg_match((string) $targetValue, (string) $sourceValue) === 1;
    }
}
