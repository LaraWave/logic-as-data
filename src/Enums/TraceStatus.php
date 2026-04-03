<?php

namespace LaraWave\LogicAsData\Enums;

enum TraceStatus: string
{
    case PASSED  = 'passed';
    case FAILED  = 'failed';
    case ERROR   = 'error';
}
