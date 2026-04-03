<?php

namespace LaraWave\LogicAsData\Services;

use Illuminate\Support\Facades\DB;

class TelemetryRecorder
{
    public function record(
        string $hook,
        array $context,
        array $traces,
        float $totalDuration
    ): void {
        if (! config('logic-as-data.analytics.enabled', true) || empty($traces)) {
            return;
        }

        $telemetryTable = config('logic-as-data.tables.telemetry');
        $tracesTable = config('logic-as-data.tables.traces');

        $now = now();

        $telemetryId = DB::table($telemetryTable)->insertGetId([
            'hook'           => $hook,
            'context'        => !empty($context) ? json_encode($context) : null,
            'total_duration' => $totalDuration,
            'created_at'     => $now,
        ]);

        $rows = array_map(function ($trace) use ($telemetryId, $now) {
            return [
                'logic_telemetry_id' => $telemetryId,
                'logic_rule_id'      => $trace['logic_rule_id'],
                'status'             => $trace['status'],
                'error'              => $trace['error'],
                'duration'           => $trace['duration'],
                'snapshots'          => json_encode($trace['snapshots']),
                'created_at'         => $now,
            ];
        }, $traces);

        DB::table($tracesTable)->insert($rows);
    }
}
