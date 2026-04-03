<?php

use LaraWave\LogicAsData\Extractors\SourceExtractor;
use LaraWave\LogicAsData\Enums\EvaluationStrategy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LaraWave\LogicAsData\Resolvers\TargetResolver;
use LaraWave\LogicAsData\Facades\LogicEngine;
use LaraWave\LogicAsData\Models\LogicRule;
use LaraWave\LogicAsData\LogicRegistry;

uses(RefreshDatabase::class);

// ---------------------------------------------------------
// DEFINE MOCK CLASSES
// ---------------------------------------------------------

class MockContextExtractor extends SourceExtractor
{
    public function extract(string $alias, array $context = []): mixed
    {
        $key = str_replace('.', '_', $alias);
        return $context[$key] ?? null;
    }
}

class MockConfigResolver extends TargetResolver
{
    public function resolve(string $alias, array $context, array $params = []): mixed
    {
        return $params['default'] ?? null;
    }
}

class MockRelativeDateResolver extends TargetResolver
{
    public function resolve(string $alias, array $context, array $params = []): mixed
    {
        return now()->modify($params['modifier'])->format($params['format']);
    }
}

beforeEach(function () {
    $this->registry = app(LogicRegistry::class);

    // Register Extractors
    $this->registry->registerExtractor('cart.total', MockContextExtractor::class);
    $this->registry->registerExtractor('user.last_purchase_date', MockContextExtractor::class);
    $this->registry->registerExtractor('user.tier', MockContextExtractor::class);
    $this->registry->registerExtractor('user.lifetime_spend', MockContextExtractor::class);
    $this->registry->registerExtractor('user.status', MockContextExtractor::class);

    // Register Resolvers
    $this->registry->registerResolver('system.config', MockConfigResolver::class);
    $this->registry->registerResolver('date.relative', MockRelativeDateResolver::class);

    // ---------------------------------------------------------
    // Seed the Database with Rules for the same hook
    // ---------------------------------------------------------

    // RULE A
    LogicRule::create([
        'name' => 'Premium Upgrade Complex Rule',
        'hook' => 'checkout.premium_upgrade',
        'definition' => [
            'predicate' => [
                'combinator' => 'and',
                'clauses' => [
                    [
                        'source' => ['alias' => 'cart.total'],
                        'operator' => 'greater_than_or_equal',
                        'target' => [
                            'alias' => 'system.config',
                            'params' => [
                                'key' => 'store.premium_shipping_threshold',
                                'default' => 5000
                            ]
                        ]
                    ],
                    [
                        'source' => ['alias' => 'user.last_purchase_date'],
                        'operator' => 'greater_than_or_equal',
                        'target' => [
                            'alias' => 'date.relative',
                            'params' => [
                                'modifier' => '-6 months',
                                'format' => 'Y-m-d H:i:s'
                            ]
                        ]
                    ],
                    [
                        'combinator' => 'or',
                        'clauses' => [
                            [
                                'source' => ['alias' => 'user.tier'],
                                'operator' => 'equals',
                                'target' => [
                                    'alias' => 'core.literal',
                                    'params' => ['value' => 'vip', 'value_type' => 'string']
                                ]
                            ],
                            [
                                'source' => ['alias' => 'user.lifetime_spend'],
                                'operator' => 'greater_than',
                                'target' => [
                                    'alias' => 'core.literal',
                                    'params' => ['value' => '50000', 'value_type' => 'integer']
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'actions' => [
                [
                    'handler' => 'cart.add_free_item',
                    'params' => ['sku' => 'PREMIUM-GIFT-BAG', 'quantity' => 1]
                ]
            ]
        ],
        'priority' => 100,
        'status' => 'active',
    ]);

    // RULE B
    LogicRule::create([
        'name' => 'Premium Upgrade Fallback Rule',
        'hook' => 'checkout.premium_upgrade',
        'definition' => [
            'predicate' => [
                'source' => ['alias' => 'user.status'],
                'operator' => 'equals',
                'target' => [
                    'alias' => 'core.literal',
                    'params' => ['value' => 'active', 'value_type' => 'string']
                ]
            ],
            'actions' => []
        ],
        'priority' => 50,
        'status' => 'active',
    ]);
});

test('Strategy: ANY - Returns true if only Rule A passes', function () {
    $context = [
        // Passes Rule A (Cart > 5000, recent purchase, VIP tier)
        'cart_total' => 6000,
        'user_last_purchase_date' => now()->subMonths(2)->format('Y-m-d H:i:s'),
        'user_tier' => 'vip',
        'user_lifetime_spend' => 0,
        // Fails Rule B
        'user_status' => 'suspended' 
    ];

    // 'any' is the default strategy, so it should return true because Rule A passes
    expect(LogicEngine::passes('checkout.premium_upgrade', $context))->toBeTrue();
});

test('Strategy: ALL - Returns false if Rule A passes but Rule B fails', function () {
    $context = [
        // Passes Rule A
        'cart_total' => 6000,
        'user_last_purchase_date' => now()->subMonths(2)->format('Y-m-d H:i:s'),
        'user_tier' => 'vip',
        'user_lifetime_spend' => 0,

        // Fails Rule B
        'user_status' => 'suspended' 
    ];

    // Force strategy 'all'
    expect(
        LogicEngine::passes('checkout.premium_upgrade', $context, EvaluationStrategy::ALL)
    )->toBeFalse();
});

test('Strategy: ANY - Returns false if both rules fail', function () {
    $context = [
        // Fails Rule A (Cart too low)
        'cart_total' => 1000,
        'user_last_purchase_date' => now()->subMonths(2)->format('Y-m-d H:i:s'),
        'user_tier' => 'vip',

        // Fails Rule B
        'user_status' => 'suspended' 
    ];

    expect(LogicEngine::passes('checkout.premium_upgrade', $context))->toBeFalse();
});

test('Strategy: ALL - Returns true if both rules pass', function () {
    $context = [
        // Passes Rule A (Cart high, recent purchase, high spend instead of VIP)
        'cart_total' => 6000,
        'user_last_purchase_date' => now()->subMonths(2)->format('Y-m-d H:i:s'),
        'user_tier' => 'guest',
        'user_lifetime_spend' => 60000,
        
        // Passes Rule B
        'user_status' => 'active' 
    ];

    expect(
        LogicEngine::passes('checkout.premium_upgrade', $context, EvaluationStrategy::ALL)
    )->toBeTrue();
});
