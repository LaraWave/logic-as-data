<?php

namespace LaraWave\LogicAsData\Actions;

use LaraWave\LogicAsData\Actions\Action;
use Illuminate\Support\Facades\Log;

class LogMessageAction extends Action
{
    public function execute(string $alias, array $params, array $context = []): mixed
    {
        $level = data_get($params, 'level', 'info');
        $message = data_get($params, 'message', 'Logic-As-Data: Rule triggered.');

        return Log::log($level, $message, [
            'action' => self::class,
            'context' => $context,
            'triggered_at' => now()->toIso8601String()
        ]);
    }

    public static function metadata(string $alias): array
    {
        return [
            // label to show in the UI
            'label' => 'Log Message',

            // description shown below the field in the UI
            'description' => '',

            // The array of input fields required to configure this specific component
            'fields' => [
                'level' => [
                    'type' => 'select',
                    'label' => 'Log Level',
                    'options' => [
                        ['value' => 'info', 'label' => 'Info'],
                        ['value' => 'warning', 'label' => 'Warning'],
                        ['value' => 'error', 'label' => 'Error'],
                    ],
                    'default' => 'info'
                ],
                'message' => [
                    'type' => 'text',
                    'label' => 'Log Message',
                    'placeholder' => 'Rule was triggered.'
                ],
            ],
        ];
    }
}
