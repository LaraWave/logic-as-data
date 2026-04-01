<?php

namespace LaraWave\LogicAsData\Resolvers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Arr;

class DateTimeResolver extends TargetResolver
{
    public function resolve(string $alias, array $context, array $params = []): mixed
    {
        // Resolve Timezone from params or app default
        $tz = Arr::get($params, 'timezone', config('app.timezone'));

        // Resolve "Now" (Allowing for 'now' override in context for testing/simulation)
        $now = isset($context['now'])
            ? Carbon::parse($context['now'], $tz)
            : now($tz);

        // Resolve Formats from params
        $dateFormat = Arr::get($params, 'date_format', 'Y-m-d');
        $timeFormat = Arr::get($params, 'time_format', 'H:i');

        return match ($alias) {
            // Date
            'date.current'      => $now->format($dateFormat),
            'date.day_name'     => $now->format('l'), // e.g. Sunday
            'date.month_name'   => $now->format('F'), // e.g. March
            'date.year'         => $now->year,
            'date.month'        => $now->month,
            'date.day'          => $now->day,

            // Logical Scenarios (Booleans)
            'date.is_weekday'   => $now->isWeekday(),
            'date.is_weekend'   => $now->isWeekend(),
            'date.is_today'     => $now->isToday(),
            'date.is_future'    => $now->isFuture(),
            'date.is_past'      => $now->isPast(),
            'date.is_leap_year' => $now->isLeapYear(),

            // Time
            'time.current'      => $now->format($timeFormat),
            'time.hour'         => $now->hour, // 0-23
            'time.minute'       => $now->minute,
            'time.second'       => $now->second,

            default => null,
        };
    }

    public static function metadata(string $alias): array
    {
        $fields = [];

        // Timezone applies to almost everything
        $fields['timezone'] = [
            'type'        => 'string',
            'label'       => 'Timezone',
            'default'     => config('app.timezone'),
            'placeholder' => 'e.g., UTC, Asia/Kolkata'
        ];

        if ($alias === 'date.current') {
            $fields['date_format'] = [
                'type'        => 'string',
                'label'       => 'Date Format',
                'default'     => 'Y-m-d',
                'placeholder' => 'e.g., d/m/Y'
            ];
        }

        if ($alias === 'time.current') {
            $fields['time_format'] = [
                'type'        => 'string',
                'label'       => 'Time Format',
                'default'     => 'H:i',
                'placeholder' => 'e.g., h:i A'
            ];
        }

        return [
            'label' => str($alias)
                ->explode('.')
                ->map(fn ($part) => str($part)->headline())
                ->implode(': '),

            'description' => '',

            'fields' => $fields,
        ];
    }
}
