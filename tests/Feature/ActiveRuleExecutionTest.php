<?php

namespace LaraWave\LogicAsData\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use LaraWave\LogicAsData\Enums\RuleStatus;
use LaraWave\LogicAsData\Models\LogicRule;
use Illuminate\Support\Facades\Config;
use LaraWave\LogicAsData\Actions\Action;
use Illuminate\Foundation\Auth\User;
use LaraWave\LogicAsData\LogicRegistry;
use LaraWave\LogicAsData\Facades\LogicEngine;

uses(RefreshDatabase::class);

// ---------------------------------------------------------
// Define Mock Classes
// ---------------------------------------------------------

class MockSystemLogAction extends Action
{
    public static array $calledWith = [];

    public function execute(string $alias, array $params, array $context = []): mixed
    {
        self::$calledWith = $params;
        return true;
    }
}

class MockWebRedirectAction extends Action
{
    public static array $calledWith = [];

    public function execute(string $alias, array $params, array $context = []): mixed
    {
        self::$calledWith = $params;
        return true;
    }
}

it('triggers only active rules', function () {
    // Reset static state so previous tests don't bleed into this one
    MockSystemLogAction::$calledWith = [];
    MockWebRedirectAction::$calledWith = [];

    // Setup Config to pass the dynamic 'system.config' clauses in your Factory's JSON
    Config::set('app.timezone', 'Asia/Kolkata');
    Config::set('app.env', 'testing');
    Config::set('app.url', 'http://localhost');
    Config::set('app.asset_url', 'http://localhost');

    // Swap out the real actions in the Registry for our Mock classes
    $registry = app(LogicRegistry::class);
    $registry->registerAction('system.log', MockSystemLogAction::class);
    $registry->registerAction('web.redirect', MockWebRedirectAction::class);

    // Create one Draft rule to prove the engine correctly ignores it
    LogicRule::factory()->simpleDefinition()->create([
        'hook' => 'cart.checkout',
        'status' => RuleStatus::DRAFT
    ]);

    // Create one Active rule
    LogicRule::factory()->create([
        'hook' => 'cart.checkout',
        'status' => RuleStatus::ACTIVE
    ]);

    // Create one inactive rule to prove the engine correctly ignores it
    LogicRule::factory()->simpleDefinition()->create([
        'hook' => 'cart.checkout',
        'status' => RuleStatus::INACTIVE
    ]);

    // Construct the Context payload to successfully pass the AST Logic Conditions
    $user = new class extends User {};
    $user->forceFill([
        'email' => 'vip_customer@larawave.com',
        'email_verified_at' => now(),
        'timezone' => 'Asia/Kolkata'
    ]);
    $context = ['user' => $user];

    // Trigger the Engine
    LogicEngine::trigger('cart.checkout', $context);

    // Verify that the mock actions captured the parameters from the JSON
    expect(MockSystemLogAction::$calledWith)->not->toBeEmpty()
        ->and(MockSystemLogAction::$calledWith['level'])->toBe('info')
        ->and(MockSystemLogAction::$calledWith['message'])->toBe('Logic-As-Data: Rule triggered!');

    expect(MockWebRedirectAction::$calledWith)->not->toBeEmpty()
        ->and(MockWebRedirectAction::$calledWith['url'])->toBe('https://github.com/LaraWave/logic-as-data');
});
