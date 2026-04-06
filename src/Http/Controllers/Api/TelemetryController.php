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
