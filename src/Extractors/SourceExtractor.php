<?php

namespace LaraWave\LogicAsData\Extractors;

use Illuminate\Support\Str;

abstract class SourceExtractor
{
    /**
     * Extract the source value from the context or external state.
     *
     * @param string $alias The alias that triggered this extractor (e.g., 'auth.check', 'cart.total', 'user.roles', 'order.status' etc)
     * @param array $context The contextual data payload passed into the engine
     * @return mixed The extractd value.
     */
    abstract public function extract(string $alias, array $context = []): mixed;

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

            // The array of input fields required to configure this specific component
            'fields' => [],
        ];
    }
}
