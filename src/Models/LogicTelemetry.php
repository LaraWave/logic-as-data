<?php

namespace LaraWave\LogicAsData\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use LaraWave\LogicAsData\Models\LogicTrace;
use Illuminate\Database\Eloquent\Model;

class LogicTelemetry extends Model
{
    protected $appends = ['formatted_date'];

    protected $casts = [
        'subjects'       => 'array',
        'context'        => 'array',
        'total_duration' => 'float',
    ];

    protected function formattedDate(string $format = 'M j, g:i A'): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_at ? $this->created_at->format($format) : '-',
        );
    }

    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    public function traces(): HasMany
    {
        return $this->hasMany(LogicTrace::class, 'logic_telemetry_id');
    }

    public function getTable(): string
    {
        return config('logic-as-data.tables.telemetry');
    }
}
