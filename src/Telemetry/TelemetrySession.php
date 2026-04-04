<?php

namespace LaraWave\LogicAsData\Telemetry;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Throwable;

class TelemetrySession
{
    private string $hook;
    private array $context;
    private float $sessionStartTime;
    private array $traces = [];

    public function __construct(string $hook, array $context = [])
    {
        $this->hook = $hook;
        $this->context = $context;
        $this->sessionStartTime = microtime(true);
    }

    public function push(RuleTrace $trace, ClauseSnapshot $snapshot): void
    {
        $this->traces[] = $trace->toRecordArray($snapshot);
    }

    public function save(): void
    {
        if (! config('logic-as-data.telemetry.enabled', true) || empty($this->traces)) {
            return;
        }

        try {
            $now = now();
            $telemetryTable = config('logic-as-data.tables.telemetry', 'logic_telemetry');
            $tracesTable = config('logic-as-data.tables.traces', 'logic_traces');

            $telemetryId = DB::table($telemetryTable)->insertGetId([
                'hook'           => $this->hook,
                'context'        => !empty($this->context) ? json_encode($this->context) : null,
                'total_duration' => round((microtime(true) - $this->sessionStartTime) * 1000, 2),
                'created_at'     => $now,
            ]);

            $rows = array_map(function ($trace) use ($telemetryId, $now) {
                return [
                    'logic_telemetry_id' => $telemetryId,
                    'logic_rule_id'      => $trace['logic_rule_id'],
                    'status'             => $trace['status'],
                    'error'              => $trace['error'],
                    'duration'           => $trace['duration'],
                    'snapshot'           => json_encode($trace['snapshot']),
                    'created_at'         => $now,
                ];
            }, $this->traces);

            DB::table($tracesTable)->insert($rows);

        } catch (Throwable $e) {
            Log::warning('LogicAsData: Failed to record telemetry.', [
                'hook'  => $this->hook,
                'error' => $e->getMessage()
            ]);

            if (config('logic-as-data.telemetry.strict', false)) {
                throw $e;
            }
        }
    }
}
