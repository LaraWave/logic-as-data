<?php

namespace LaraWave\LogicAsData\Resolvers;

use LaraWave\LogicAsData\Resolvers\TargetResolver;

class ConfigResolver extends TargetResolver
{
    public function resolve(string $alias, array $context, array $params = []): mixed
    {
        $key = $params['key'] ?? null;
        $default = $params['default'] ?? null;

        if (! $key) {
            return $default;
        }

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

            // The array of input fields required to configure this target resolver
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
