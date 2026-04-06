<?php

namespace LaraWave\LogicAsData\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LaraWave\LogicAsData\Models\LogicTelemetry;
use LaraWave\LogicAsData\Models\LogicRule;
use Illuminate\Database\Eloquent\Model;

class LogicTrace extends Model
{
    protected $casts = [
        'snapshot' => 'array',
        'duration' => 'float',
    ];

    public function telemetry(): BelongsTo
    {
        return $this->belongsTo(LogicTelemetry::class, 'logic_telemetry_id');
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(LogicRule::class, 'logic_rule_id');
    }
}
