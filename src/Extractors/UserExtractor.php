<?php

namespace LaraWave\LogicAsData\Extractors;

use LaraWave\LogicAsData\Extractors\SourceExtractor;
use Illuminate\Support\Str;

class UserExtractor extends SourceExtractor
{
    public function extract(string $alias, array $context = []): mixed
    {
        $user = $context['user'] ?? auth()->user();

        if (! $user) {
            return null;
        }

        return match ($alias) {
            'user.id' => $user->id,
            'user.name' => $user->name,
            'user.email' => $user->email,
            'user.is_verified' => ! is_null($user->email_verified_at),
            default => null,
        };
    }

    public static function metadata(string $alias): array
    {
        return [
            // label to show in the UI
            'label' => Str::headline(str_replace('.', ' ', $alias)),

            // description shown below the field in the UI
            'description' => '',

            // The array of input fields required to configure this specific component
            'fields' => [],
        ];
    }
}
