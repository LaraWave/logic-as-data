<?php

namespace LaraWave\LogicAsData\Enums;

enum ActionStatus: string
{
    case SUCCESS   = 'success';   // Executed perfectly
    case FAILED    = 'failed';    // Threw an exception
    case UNREACHED = 'unreached'; // Engine crashed/stopped before getting here
}
