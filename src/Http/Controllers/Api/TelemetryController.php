<?php

namespace LaraWave\LogicAsData\Http\Controllers\Api;

use LaraWave\LogicAsData\Models\LogicTelemetry;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TelemetryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        \LaraWave\LogicAsData\Facades\LogicEngine::trigger('payment.failed', ['user' => (new \Illuminate\Foundation\Auth\User)->forceFill(['email' => 'vip_customer@larawave.com','email_verified_at' => now(),'timezone' => 'Asia/Kolkata'])], [(new \Illuminate\Foundation\Auth\User)->forceFill(['email' => 'vip_customer@larawave.com','email_verified_at' => now(),'timezone' => 'Asia/Kolkata', 'id' => 1010]), ['promo_code' => 'TBK-999'], ['lorem-ipsum' => 'Dolor sit amet']]);
        \LaraWave\LogicAsData\Facades\LogicEngine::trigger('auth.login', ['user' => (new \Illuminate\Foundation\Auth\User)->forceFill(['email' => 'vip_customer@larawave.com','email_verified_at' => now(),'timezone' => 'Asia/Kolkata'])], [(new \Illuminate\Foundation\Auth\User)->forceFill(['email' => 'vip_customer@larawave.com','email_verified_at' => now(),'timezone' => 'Asia/Kolkata', 'id' => 1010]), ['promo_code' => 'TBK-999'], ['lorem-ipsum' => 'Dolor sit amet']]);
        \LaraWave\LogicAsData\Facades\LogicEngine::trigger('auth.logout', ['user' => (new \Illuminate\Foundation\Auth\User)->forceFill(['email' => 'vip_customer@larawave.com','email_verified_at' => now(),'timezone' => 'Asia/Kolkata'])], [(new \Illuminate\Foundation\Auth\User)->forceFill(['email' => 'vip_customer@larawave.com','email_verified_at' => now(),'timezone' => 'Asia/Kolkata', 'id' => 1010]), ['promo_code' => 'TBK-999'], ['lorem-ipsum' => 'Dolor sit amet']]);
        \LaraWave\LogicAsData\Facades\LogicEngine::trigger('order.created', ['user' => (new \Illuminate\Foundation\Auth\User)->forceFill(['email' => 'vip_customer@larawave.com','email_verified_at' => now(),'timezone' => 'Asia/Kolkata'])], [(new \Illuminate\Foundation\Auth\User)->forceFill(['email' => 'vip_customer@larawave.com','email_verified_at' => now(),'timezone' => 'Asia/Kolkata', 'id' => 1010]), ['promo_code' => 'TBK-999'], ['lorem-ipsum' => 'Dolor sit amet']]);

        $logs = LogicTelemetry::query()
            ->with('causer')
            ->latest()
            ->paginate(20)
            ->onEachSide(1);

        return response()->json(['data' => $logs]);
    }

    public function show(int $id): JsonResponse
    {
        $log = LogicTelemetry::with(['causer', 'traces.rule'])->findOrFail($id);

        return response()->json(['data' => $log]);
    }
}
