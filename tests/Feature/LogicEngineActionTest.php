<?php

namespace LaraWave\LogicAsData\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use LaraWave\LogicAsData\Extractors\SourceExtractor;
use LaraWave\LogicAsData\Facades\LogicEngine;
use LaraWave\LogicAsData\Models\LogicRule;
use LaraWave\LogicAsData\Actions\Action;
use LaraWave\LogicAsData\LogicRegistry;

uses(RefreshDatabase::class);

// ---------------------------------------------------------
// DEFINE MOCK CLASSES
// ---------------------------------------------------------

// A dummy extractor that just returns true or false based on the context
class MockBooleanExtractor extends SourceExtractor
{
    public function extract(string $alias, array $context = []): mixed
    {
        return $context['should_pass'] ?? false;
    }
}

// A mock action that records when it was called and what params it received
class MockCartAction extends Action
{
    public static array $calledWith = [];

    public function execute(string $alias, array $params, array $context = []): mixed
    {
        // Record the execution for assertions
        self::$calledWith[] = $params;
        return true;
    }
}

class MockNotificationAction extends Action
{
    public static bool $wasCalled = false;

    public function execute(string $alias, array $params, array $context = []): mixed
    {
        self::$wasCalled = true;
        return true;
    }
}

beforeEach(function () {
    $this->registry = app(LogicRegistry::class);

    // Reset static mock states before each test
    MockCartAction::$calledWith = [];
    MockNotificationAction::$wasCalled = false;

    // Register mocks
    $this->registry->registerExtractor('test.boolean', MockBooleanExtractor::class);
    $this->registry->registerAction('cart.add_free_item', MockCartAction::class);
    $this->registry->registerAction('user.notify', MockNotificationAction::class);

    // Create a Rule With Actions
    LogicRule::create([
        'name' => 'Free Gift Rule',
        'hook' => 'checkout.process',
        'definition' => [
            'predicate' => [
                'source' => ['alias' => 'test.boolean'],
                'operator' => 'equals',
                'target' => [
                    'alias' => 'core.literal',
                    'params' => ['value' => true, 'value_type' => 'boolean']
                ]
            ],
            'actions' => [
                [
                    'alias' => 'cart.add_free_item',
                    'params' => ['sku' => 'GIFT-123']
                ],
                [
                    'alias' => 'user.notify',
                    'params' => ['message' => 'You got a free gift!']
                ]
            ]
        ],
        'priority' => 100,
        'status' => 'active',
    ]);
});

test('Actions execute when the predicate passes', function () {
    // Trigger the engine
    LogicEngine::trigger('checkout.process', ['should_pass' => true]);

    // Assert the first action was called with correct params
    expect(MockCartAction::$calledWith)->toHaveCount(1);
    expect(MockCartAction::$calledWith[0]['sku'])->toBe('GIFT-123');

    // Assert the second action was also called
    expect(MockNotificationAction::$wasCalled)->toBeTrue();
});

test('Actions DO NOT execute when the predicate fails', function () {
    // Trigger the engine with context that makes the rule fail
    LogicEngine::trigger('checkout.process', ['should_pass' => false]);

    // Assert neither action was fired
    expect(MockCartAction::$calledWith)->toBeEmpty();
    expect(MockNotificationAction::$wasCalled)->toBeFalse();
});
