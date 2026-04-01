<?php

use LaraWave\LogicAsData\Operators\GreaterThanOperator;
use LaraWave\LogicAsData\Evaluators\PredicateEvaluator;
use LaraWave\LogicAsData\Extractors\SourceExtractor;
use LaraWave\LogicAsData\Extractors\UserExtractor;
use LaraWave\LogicAsData\Operators\EqualsOperator;
use Illuminate\Foundation\Auth\User;
use LaraWave\LogicAsData\LogicRegistry;

// ---------------------------------------------------------
// DEFINE MOCK CLASSES
// ---------------------------------------------------------

class MockUserTimezoneExtractor extends SourceExtractor
{
    public function extract(string $alias, array $context = []): mixed
    {
        return $context['_params']['default'] ?? null;
    }
}

beforeEach(function () {
    $this->registry = app(LogicRegistry::class);
    $this->registry->registerExtractor('user.timezone', MockUserTimezoneExtractor::class);
});

test('it evaluates complex nested predicates', function () {
    $evaluator = $this->registry->evaluator('default');
    config([
        'app.url' => 'http://test.local',
        'app.asset_url' => 'http://test.local',
    ]);

    // Scenario: (User is Admin) AND (Cart > 5000 OR User has Lifetime Spend > 10000)
    $predicate = [
        'combinator' => 'and',
        'clauses' => [
            [
                'source' => ['alias' => 'user.email', 'params' => []],
                'operator' => 'ends_with',
                'target' => [
                    'alias' => 'core.literal',
                    'params' => ['value' => '@larawave.com', 'value_type' => 'string']
                ]
            ],
            [
                'source' => ['alias' => 'user.is_verified'],
                'operator' => 'equals',
                'target' => [
                    'alias' => 'core.literal',
                    'params' => ['value' => true, 'value_type' => 'boolean']
                ]
            ],
            [
                'combinator' => 'or',
                'clauses' => [
                    [
                        "source" => [
                            "alias" => "system.config",
                            "params" => [
                                "key" => "app.timezone",
                                "default" => "UTC"
                            ]
                        ],
                        "operator" => "equals",
                        "target" => [
                            'alias' => 'core.literal',
                            'params' => ['value_type' => 'string', 'value' => 'Asia/Kolkata']
                        ]
                    ],
                    [
                        "source" => [
                            "alias" => "system.config",
                            "params" => [
                                "key" => "app.env",
                                "default" => "production"
                            ]
                        ],
                        "operator" => "equals",
                        "target" => [
                            'alias' => 'core.literal',
                            'params' => ['value_type' => 'string', 'value' => 'testing']
                        ]
                    ],
                ]
            ],
            [
                'combinator' => 'or',
                'clauses' => [
                    [
                        "source" => ["alias" => "user.timezone"],
                        "operator" => "equals",
                        "target" => [
                            "alias" => "system.config",
                            "params" => [
                                "key" => "app.timezone",
                                "default" => "UTC"
                            ]
                        ]
                    ],
                    [
                        "source" => [
                            "alias" => "system.config",
                            "params" => ["key" => "app.url"]
                        ],
                        "operator" => "equals",
                        "target" => [
                            "alias" => "system.config",
                            "params" => [
                                "key" => "app.asset_url",
                                "default" => "http://localhost"
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ];

    // Context 1: Should pass
    $user1 = new class extends User {};
    $user1->forceFill([
        'email' => 'test@larawave.com',
        'email_verified_at' => now(),
        'timezone' => 'UTC'
    ]);
    $context1 = [
        'user' => $user1
    ];

    // Context 2: Should fail
    $user2 = new class extends User {};
    $user2->forceFill([
        'email' => 'test@example.net',
        'timezone' => 'Asia/Kolkata'
    ]);
    $context2 = [
        'user' => $user2
    ];

    // Context 3: Should fail
    $user3 = new class extends User {};
    $user3->forceFill([
        'email' => 'demo@larawave.com',
        'email_verified_at' => now(),
        'timezone' => 'Asia/Kolkata'
    ]);
    $context3 = [
        'user' => $user3,
        'test' => 'demo'
    ];

    expect($evaluator->evaluate($predicate, $context1))->toBeTrue();
    expect($evaluator->evaluate($predicate, $context2))->toBeFalse();
    expect($evaluator->evaluate($predicate, $context3))->toBeTrue();
});
