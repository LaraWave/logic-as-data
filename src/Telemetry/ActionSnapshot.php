<?php

namespace LaraWave\LogicAsData\Telemetry;

use LaraWave\LogicAsData\Enums\ActionStatus;

class ActionSnapshot
{
    private string $alias;
    private array $params;
    private ActionStatus $status = ActionStatus::UNREACHED;
    private ?float $duration = null;
    private ?string $error = null;

    public function __construct(string $alias, array $params = [])
    {
        $this->alias = $alias;
        $this->params = $params;
    }

    public function markSuccess(float $durationMs): void
    {
        $this->status = ActionStatus::SUCCESS;
        $this->duration = round($durationMs, 2);
    }

    public function markFailed(float $durationMs, string $error): void
    {
        $this->status = ActionStatus::FAILED;
        $this->duration = round($durationMs, 2);
        $this->error = $error;
    }

    public function toArray(): array
    {
        $findings = ['status' => $this->status, 'duration' => $this->duration];
        if ($this->error) {
            $findings['error'] = $this->error;
        }

        return [
            'alias'    => $this->alias,
            'params'   => $this->params,
            '_finding' => $findings,
        ];
    }
}
