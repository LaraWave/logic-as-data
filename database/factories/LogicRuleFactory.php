<?php

namespace LaraWave\LogicAsData\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use LaraWave\LogicAsData\Enums\RuleStatus;
use LaraWave\LogicAsData\Models\LogicRule;

/**
 * @extends Factory<Model>
 */
class LogicRuleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LogicRule::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Default Testing Rule - ' . $this->faker->words(3, true),
            'hook' => 'cart.checkout',
            'priority' => $this->faker->numberBetween(0, 50),
            'status' => RuleStatus::DRAFT,
            'definition' => [
                'predicate' => [
                    'combinator' => 'and',
                    'clauses' => [
                        [
                            'source' => ['alias' => 'user.email'],
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
                                    'source' => [
                                        'alias' => 'system.config',
                                        'params' => [
                                            'key' => 'app.timezone',
                                            'default' => 'UTC'
                                        ]
                                    ],
                                    'operator' => 'equals',
                                    "target" => [
                                        'alias' => 'core.literal',
                                        'params' => ['value_type' => 'string', 'value' => 'Asia/Kolkata']
                                    ]
                                ],
                                [
                                    'source' => [
                                        'alias' => 'system.config',
                                        'params' => [
                                            'key' => 'app.env',
                                            'default' => 'production'
                                        ]
                                    ],
                                    'operator' => 'equals',
                                    "target" => [
                                        'alias' => 'core.literal',
                                        'params' => [
                                            'value_type' => 'string',
                                            'value' => 'testing'
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        [
                            'combinator' => 'or',
                            'clauses' => [
                                [
                                    'source' => ['alias' => 'user.email'],
                                    'operator' => 'contains',
                                    'target' => [
                                        'alias' => 'core.literal',
                                        'params' => [
                                            'value' => '@larawave.com',
                                            'value_type' => 'string'
                                        ]
                                    ]
                                ],
                                [
                                    'source' => [
                                        'alias' => 'system.config',
                                        'params' => ['key' => 'app.url']
                                    ],
                                    'operator' => 'equals',
                                    'target' => [
                                        'alias' => 'system.config',
                                        'params' => [
                                            'key' => 'app.asset_url',
                                            'default' => 'http://localhost'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'actions' => [
                    [
                        'alias' => 'system.log',
                        'params' => [
                            'level' => 'info',
                            'message' => 'Logic-As-Data: Rule triggered!'
                        ]
                    ],
                    [
                        'alias' => 'web.redirect',
                        'params' => [
                            'url' => 'https://github.com/LaraWave/logic-as-data'
                        ]
                    ]
                ]
            ],
        ];
    }

    public function simpleDefinition(): static
    {
        return $this->state(fn (array $attributes) => [
            'definition' => [
                'predicate' => [
                    'combinator' => 'and',
                    'clauses' => [
                        [
                            'source' => ['alias' => 'user.is_verified'],
                            'operator' => 'equals',
                            'target' => true
                        ]
                    ]
                ],
                'actions' => [
                    [
                        'alias' => 'system.log',
                        'params' => ['message' => 'User is verified.']
                    ]
                ]
            ],
        ]);
    }
}
