<?php

namespace LaraWave\LogicAsData\Resolvers;

use Illuminate\Support\Arr;

class LiteralResolver extends TargetResolver
{
    public function resolve(string $alias, array $context, array $params = []): mixed
    {
        $value = Arr::get($params, 'value');
        $type = Arr::get($params, 'value_type', 'string');

        return match ($type) {
            'integer' => (int) $value,
            'float'   => (float) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            default   => (string) $value,
        };
    }

    public static function metadata(string $alias): array
    {
        return [
            'label' => 'Custom Value',
            'description' => 'A fixed value provided directly by the user.',
            'fields' => [
                'value_type' => [
                    'type'    => 'select',
                    'label'   => 'Data Type',
                    'default' => 'string',
                    'options' => [
                        ['value' => 'string', 'label' => 'Text'],
                        ['value' => 'integer', 'label' => 'Whole Number'],
                        ['value' => 'float', 'label' => 'Decimal Number'],
                        ['value' => 'boolean', 'label' => 'Yes/No (True/False)'],
                    ],
                ],
                'value' => [
                    'type'         => 'dynamic',
                    'label'        => 'Value',
                    'placeholder'  => 'Enter the value',
                    'depends_on'   => 'value_type',
                    'type_map'     => [
                        'string'   => 'text',
                        'integer'  => 'number',
                        'float'    => 'number',
                        'boolean'  => 'boolean',
                    ],
                    'attributes_map' => [
                        'float'   => ['step' => 'any'],
                        'integer' => ['step' => '1'], 
                    ],
                    'default_type' => 'text',
                ],
            ],
        ];
    }
}
