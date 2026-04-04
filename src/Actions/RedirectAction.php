<?php

namespace LaraWave\LogicAsData\Actions;

use LaraWave\LogicAsData\Actions\Action;

class RedirectAction extends Action
{
    public function execute(string $alias, array $params, array $context = []): mixed
    {
        if (app()->runningInConsole()) {
            return false;
        }

        $url = data_get($params, 'url', '/');
        $statusCode = (int) data_get($params, 'status_code', 302);

        return redirect()->to($url, $statusCode);
    }

    public static function metadata(string $alias): array
    {
        return [
            // label to show in the UI
            'label' => 'Redirect User',

            // description shown below the field in the UI
            'description' => '',

            // The array of input fields required to configure this specific component
            'fields' => [
                'url' => [
                    'type' => 'text',
                    'label' => 'Target URL',
                    'placeholder' => '/home'
                ],
                'status' => [
                    'type' => 'number',
                    'label' => 'HTTP Status',
                    'default' => 302
                ]
            ],
        ];
    }
}
