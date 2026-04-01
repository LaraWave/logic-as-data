<?php

namespace LaraWave\LogicAsData\Tests\Unit;

use LaraWave\LogicAsData\Operators\GreaterThanOperator;
use LaraWave\LogicAsData\Operators\BetweenOperator;
use LaraWave\LogicAsData\Operators\InArrayOperator;
use LaraWave\LogicAsData\Operators\EqualsOperator;

test('operator evaluates correctly', function ($operatorClass, $source, $target, $expected) {
    $operator = new $operatorClass();

    expect($operator->check($source, $target))->toBe($expected);
})->with([
    // Equals
    [EqualsOperator::class, 'vip', 'vip', true],
    [EqualsOperator::class, 'vip', 'guest', false],

    // Greater Than
    [GreaterThanOperator::class, 100, 50, true],
    [GreaterThanOperator::class, 50, 100, false],
    [GreaterThanOperator::class, 100, 100, false],

    // In Array
    [InArrayOperator::class, 'admin', ['admin', 'editor'], true],
    [InArrayOperator::class, 'guest', ['admin', 'editor'], false],

    // Between
    [BetweenOperator::class, 10, [10, 100], true],
    [BetweenOperator::class, 50, [10, 100], true],
    [BetweenOperator::class, 100, [10, 100], true],
    [BetweenOperator::class, 101, [10, 100], false],
]);
