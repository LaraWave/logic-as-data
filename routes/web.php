<?php

use LaraWave\LogicAsData\Http\Controllers\Api\LogicRuleController;
use LaraWave\LogicAsData\Http\Controllers\Api\TelemetryController;
use LaraWave\LogicAsData\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::prefix(config('logic-as-data.route.prefix'))
    ->middleware(array_merge(config('logic-as-data.route.middleware'), ['can:viewLogicAsData']))
    ->as('logic-as-data.')
    ->group(function () {
        Route::prefix('api')->as('api.')->group(function () {
            Route::patch('logic-rules/{id}/restore', [LogicRuleController::class, 'restore'])
                ->name('logic-rules.restore');

            Route::apiResource('logic-rules', LogicRuleController::class)
                ->names('logic-rules');

            Route::apiResource('telemetry', TelemetryController::class)
                ->names('telemetry')->only(['index', 'show']);
        });

        Route::get('/{any?}', AdminController::class)
            ->where('any', '.*')
            ->name('logic-as-data.admin');
    });
