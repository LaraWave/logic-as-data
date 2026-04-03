<?php

namespace LaraWave\LogicAsData\Http\Controllers\Api;

use LaraWave\LogicAsData\Models\LogicRule;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogicRuleController extends Controller
{
    public function index(Request $request)
    {
        $logicRules = LogicRule::orderBy('hook')->orderByDesc('priority');

        if ($request->query('trashed') === 'only') {
            $logicRules->onlyTrashed();
        }

        $logicRules = $logicRules->get()->groupBy('hook');

        return response()->json(['data' => $logicRules]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'hook'                  => 'required|string|max:255',
            'status'                => 'required|string|in:draft,testing,active,inactive,archived',
            'priority'              => 'nullable|integer',
            'definition'            => 'required|array',
            'definition.predicate'  => 'required|array',
            'definition.actions'    => 'array',
        ]);

        $logicRule = LogicRule::create($validated);

        return response()->json([
            'message' => 'Rule created successfully.',
            'data'    => $logicRule
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $logicRule = LogicRule::findOrFail($id);

        return response()->json(['data' => $logicRule]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $logicRule = LogicRule::findOrFail($id);

        $validated = $request->validate([
            'name'                  => 'sometimes|required|string|max:255',
            'hook'                  => 'sometimes|required|string|max:255',
            'status'                => 'sometimes|required|string|in:draft,testing,active,inactive,archived',
            'priority'              => 'nullable|integer',
            'definition'            => 'sometimes|required|array',
            'definition.predicate'  => 'sometimes|required|array',
            'definition.actions'    => 'sometimes|array',
        ]);

        $logicRule->update($validated);

        return response()->json([
            'message' => 'Rule updated successfully.',
            'data'    => $logicRule
        ]);
    }

    public function destroy($id)
    {
        $rule = LogicRule::withTrashed()->findOrFail($id);
        $rule->delete();

        return response()->json(['data' => $rule]);
    }

    public function restore($id)
    {
        $rule = LogicRule::withTrashed()->findOrFail($id);
        $rule->restore();

        return response()->json(['data' => $rule]);
    }
}
