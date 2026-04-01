<?php

namespace LaraWave\LogicAsData\Extractors;

class ConfigExtractor extends SourceExtractor
{
    public function extract(string $alias, array $context = []): mixed
    {
        // Extract the target key and fallback from the rule's settings payload
        $key = $context['_params']['key'] ?? null;
        $default = $context['_params']['default'] ?? null;

        if (empty($key)) {
            return $default;
        }

        // Return the raw config value exactly as it exists in Laravel
        return config($key, $default);
    }

    public static function metadata(string $alias): array
    {
        $availableConfigs = [
            'app.env' => 'Application Environment',
            'app.locale' => 'Default System Language',
            'app.timezone' => 'Server Timezone',
            'app.url' => 'Application URL',
        ];

        return [
            // label to show in the UI
            'label' => 'System Config',

            // description shown below the field in the UI
            'description' => '',

            // The array of input fields required to configure this specific component
            'fields' => [
                'key' => [
                    'type' => 'select',
                    'label' => 'System Config',
                    'options' => $availableConfigs,
                    'default' => '',
                    'help' => 'Select the global configuration value to compare.',
                    'required' => true,
                ],
                'default' => [
                    'type' => 'string',
                    'label' => 'Fallback Default',
                    'default' => '',
                    'placeholder' => 'Value if key is missing',
                    'help' => 'If the setting is missing, use this value instead.',
                    'required' => false,
                ],
            ],
        ];
    }
}
