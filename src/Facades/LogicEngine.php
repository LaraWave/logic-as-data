<?php

namespace LaraWave\LogicAsData\Facades;

use LaraWave\LogicAsData\LogicEngine as CoreLogicEngine;
use Illuminate\Support\Facades\Facade;

class LogicEngine extends Facade
{
    /**
     * Get the registered name of the component inside the container.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return CoreLogicEngine::class;
    }
}
