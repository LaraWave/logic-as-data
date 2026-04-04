<?php

namespace LaraWave\LogicAsData\Observers;

use LaraWave\LogicAsData\Models\LogicRule;
use LaraWave\LogicAsData\LogicEngine;

class LogicRuleObserver
{
    public function __construct(private LogicEngine $engine)
    {}

    public function saved(LogicRule $rule): void
    {
        if ($rule->hook) {
            $this->engine->clearCacheForHook($rule->hook);
        }

        if ($rule->isDirty('hook') && $rule->getOriginal('hook')) {
            $this->engine->clearCacheForHook($rule->getOriginal('hook'));
        }
    }

    public function deleted(LogicRule $rule): void
    {
        if ($rule->hook) {
            $this->engine->clearCacheForHook($rule->hook);
        }
    }
}
