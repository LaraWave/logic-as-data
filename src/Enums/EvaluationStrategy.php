<?php

namespace LaraWave\LogicAsData\Enums;

enum EvaluationStrategy: string
{
    /**
     * Logical OR: The hook passes if AT LEAST ONE rule evaluates to true.
     */
    case ANY = 'any';

    /**
     * Logical AND: The hook passes ONLY IF ALL rules evaluate to true.
     */
    case ALL = 'all';
}
