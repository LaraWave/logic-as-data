<?php

namespace LaraWave\LogicAsData\Telemetry;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use JsonSerializable;
use Throwable;
use Closure;

class TelemetryRecorder
{
    private string $hook;
    private array $context;
    private array $subjects;
    private float $sessionStartTime;
    private array $traces = [];
    private static ?string $processRequestId = null;
    private const REQUEST_IDENTIFIER = 'logic_as_data_telemetry_key';

    public function __construct(string $hook, array $context = [], array $subjects = [])
    {
        $this->hook = $hook;
        $this->context = $context;
        $this->subjects = $subjects;
        $this->sessionStartTime = microtime(true);
    }

    public function add(RuleTrace $trace, ClauseSnapshot $clauseSnapshot): void
    {
        $this->traces[] = $trace->toRecordArray($clauseSnapshot);
    }

    public function record(): void
    {
        if (! config('logic-as-data.telemetry.enabled', true) || empty($this->traces)) {
            return;
        }

        try {
            $now = now();
            $telemetryTable = config('logic-as-data.tables.telemetry', 'logic_telemetry');
            $tracesTable = config('logic-as-data.tables.traces', 'logic_traces');

            $sessionId = request()->hasSession() ? request()->session()->getId() : null;
            $requestId = $this->resolveRequestId();
            $causer = auth()->user();

            $formattedSubjects = $this->formatSubject($this->subjects);
            $sanitizedContext = $this->sanitizeContext($this->context);

            $telemetryId = DB::table($telemetryTable)->insertGetId([
                'hook'           => $this->hook,
                'session_id'     => $sessionId,
                'request_id'     => $requestId,
                'causer_type'    => $causer ? $causer->getMorphClass() : null,
                'causer_id'      => $causer ? $causer->getAuthIdentifier() : null,
                'subjects'       => !empty($formattedSubjects) ? json_encode($formattedSubjects) : null,
                'context'        => !empty($sanitizedContext) ? json_encode($sanitizedContext) : null,
                'total_duration' => round((microtime(true) - $this->sessionStartTime) * 1000, 2),
                'created_at'     => $now,
            ]);

            $rows = array_map(function ($trace) use ($telemetryId, $now) {
                return [
                    'logic_telemetry_id' => $telemetryId,
                    'logic_rule_id'      => $trace['logic_rule_id'],
                    'status'             => $trace['status'],
                    'error'              => $trace['error'],
                    'duration'           => $trace['duration'],
                    'snapshot'           => json_encode($trace['snapshot']),
                    'created_at'         => $now,
                ];
            }, $this->traces);

            DB::table($tracesTable)->insert($rows);

        } catch (Throwable $e) {
            Log::warning('LogicAsData: Failed to record telemetry.', [
                'hook'  => $this->hook,
                'error' => $e->getMessage()
            ]);

            if (config('logic-as-data.telemetry.strict', false)) {
                throw $e;
            }
        }
    }

    private function resolveRequestId(): string
    {
        if (app()->runningInConsole()) {
            self::$processRequestId ??= (string) Str::uuid();
            return self::$processRequestId;
        }

        $request = request();

        if (! $request->attributes->has(self::REQUEST_IDENTIFIER)) {
            $request->attributes->set(self::REQUEST_IDENTIFIER, (string) Str::uuid());
        }

        return $request->attributes->get(self::REQUEST_IDENTIFIER);
    }

    private function formatSubject(mixed $subject): mixed
    {
        if ($subject instanceof Model) {
            return [
                'type' => $subject->getMorphClass(),
                'id'   => $subject->getKey(),
            ];
        }

        if ($subject instanceof Closure) {
            return '[Closure]';
        }

        if (is_resource($subject)) {
            return '[Resource]';
        }

        if (is_object($subject)) {
            $identity = ['type' => get_class($subject)];

            if (method_exists($subject, 'getKey')) {
                $identity['id'] = $subject->getKey();
            } elseif (isset($subject->id)) {
                $identity['id'] = $subject->id;
            } elseif (isset($subject->uuid)) {
                $identity['id'] = $subject->uuid;
            } else {
                if (!($subject instanceof JsonSerializable)) {
                    return '[Object: ' . get_class($subject) . ']';
                }
            }

            return $identity;
        }

        if (is_array($subject)) {
            $formatted = [];
            foreach ($subject as $key => $value) {
                $formatted[$key] = $this->formatSubject($value); 
            }
            return $formatted;
        }

        return $subject;
    }

    private function sanitizeContext(array $context): array
    {
        $sanitized = [];

        foreach ($context as $key => $value) {
            if ($value instanceof Closure) {
                $sanitized[$key] = '[Closure]';
                continue;
            }

            if (is_resource($value)) {
                $sanitized[$key] = '[Resource]';
                continue;
            }

            if ($value instanceof Arrayable) {
                $sanitized[$key] = $value->toArray();
                continue;
            }

            if (is_object($value) && !($value instanceof JsonSerializable)) {
                $sanitized[$key] = '[Object: ' . get_class($value) . ']';
                continue;
            }

            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeContext($value); 
                continue;
            }

            $sanitized[$key] = $value;
        }

        return $sanitized;
    }
}
