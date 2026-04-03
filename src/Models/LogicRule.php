<?php

namespace LaraWave\LogicAsData\Models;

use LaraWave\LogicAsData\Database\Factories\LogicRuleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaraWave\LogicAsData\Enums\RuleStatus;
use Illuminate\Database\Eloquent\Model;

class LogicRule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'hook',
        'status',
        'priority',
        'definition',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => RuleStatus::class, // Auto-casts DB string to the RuleStatus Enum
        'definition' => 'array', // Auto-decodes the definition JSON
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return LogicRuleFactory::new();
    }

    /**
     * Get the table associated with the model dynamically.
     *
     * @return string
     */
    public function getTable(): string
    {
        return config('logic-as-data.tables.rules', 'logic_rules');
    }
}
