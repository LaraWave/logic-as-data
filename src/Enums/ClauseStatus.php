<?php

namespace LaraWave\LogicAsData\Enums;

enum ClauseStatus: string
{
    case PASSED  = 'passed';
    case FAILED  = 'failed';
    case ERROR   = 'error';
    case SKIPPED = 'skipped';
}
