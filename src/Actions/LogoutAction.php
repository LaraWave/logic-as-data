<?php

namespace LaraWave\LogicAsData\Actions;

use LaraWave\LogicAsData\Actions\Action;
use Illuminate\Support\Facades\Auth;

class LogoutAction extends Action
{
    public function execute(string $alias, array $params, array $context = []): mixed
    {
        return Auth::logout();
    }

    public static function metadata(string $alias): array
    {
        return [
            // label to show in the UI
            'label' => 'Logout User',

            // description shown below the field in the UI
            'description' => '',

            // The array of input fields required to configure this specific component
            'fields' => [],
        ];
    }
}
