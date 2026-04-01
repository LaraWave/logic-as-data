<?php

namespace LaraWave\LogicAsData\Operators;

use Illuminate\Support\Str;

abstract class Operator
{
    /**
     * Check the extracted source value against the target rule value.
     *
     * @param mixed $sourceValue   The value returned by the Extractor.
     * @param mixed $targetValue The target value defined in the JSON rule.
     * @return bool
     */
    abstract public function check(mixed $sourceValue, mixed $targetValue = null): bool;

    /**
     * Provide the UI configuration for this component.
     * This method returns the metadata required by the frontend to 
     * render the appropriate labels, help text etc.
     *
     * @param  string  $alias  The registered alias for this class (e.g., 'equals', 'in_array')
     * @return array{label: string, description: string}
     */
    public static function metadata(string $alias): array
    {
        return [
            // label to show in the UI
            'label' => Str::headline($alias),

            // description shown below the field in the UI
            'description' => '',
        ];
    }
}
