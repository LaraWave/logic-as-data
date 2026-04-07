<?php

namespace LaraWave\LogicAsData\Resolvers;

use Illuminate\Support\Str;

abstract class TargetResolver
{
    /**
     * Resolve the dynamic target value.
     */
    abstract public function resolve(string $alias, array $context, array $params = []): mixed;

    /**
     * Provide the UI configuration for this component.
     * This method returns the metadata required by the frontend to 
     * render the appropriate labels, help text and dynamic input fields.
     *
     * @param  string  $alias  The registered alias for this class (e.g., 'system.config')
     * @return array{label: string, description: string, fields: array}
     */
    public static function metadata(string $alias): array
    {
        return [
            // label to show in the UI
            'label' => Str::headline(str_replace('.', ' ', $alias)),

            // description shown below the field in the UI
            'description' => '',

            // The array of input fields required to configure this target resolver
            'fields' => [],
        ];
    }
}
