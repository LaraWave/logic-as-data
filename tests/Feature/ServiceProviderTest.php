<?php

use LaraWave\LogicAsData\Resolvers\ConfigResolver;
use LaraWave\LogicAsData\Evaluators\PredicateEvaluator;
use LaraWave\LogicAsData\Operators\GreaterThanOperator;
use LaraWave\LogicAsData\Extractors\DateTimeExtractor;
use LaraWave\LogicAsData\Operators\StartsWithOperator;
use LaraWave\LogicAsData\LogicAsDataCoreServiceProvider;
use LaraWave\LogicAsData\Operators\ContainsOperator;
use LaraWave\LogicAsData\Operators\EqualsOperator;
use LaraWave\LogicAsData\Extractors\UserExtractor;
use LaraWave\LogicAsData\Actions\LogMessageAction;
use LaraWave\LogicAsData\Actions\RedirectAction;
use LaraWave\LogicAsData\Actions\LogoutAction;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Artisan;
use LaraWave\LogicAsData\LogicRegistry;
use LaraWave\LogicAsData\LogicEngine;

test('it registers LogicRegistry and LogicEngine as singletons', function () {
    // Assert they are bound in the Laravel container
    expect(app()->bound(LogicRegistry::class))->toBeTrue();
    expect(app()->bound(LogicEngine::class))->toBeTrue();

    // Assert they are true singletons (resolving twice gives the exact same instance)
    $registry1 = app(LogicRegistry::class);
    $registry2 = app(LogicRegistry::class);
    expect($registry1)->toBe($registry2);

    $engine1 = app(LogicEngine::class);
    $engine2 = app(LogicEngine::class);
    expect($engine1)->toBe($engine2);
});

test('it merges the default configuration', function () {
    // Assert the config is loaded into Laravel's config repository
    $config = config('logic-as-data');

    expect($config)->toBeArray();

    expect(config('logic-as-data.cache'))->toBeArray();
    expect(config('logic-as-data.table_name'))->toBeString();
});

test('it boots and registers all default core components into the registry', function () {
    $registry = app(LogicRegistry::class);

    // Test some of the extractor keys to ensure they are registered successfully
    expect($registry->extractor('user.id'))->toBeInstanceOf(UserExtractor::class);
    expect($registry->extractor('date.current'))->toBeInstanceOf(DateTimeExtractor::class);
    expect($registry->extractor('time.minute'))->toBeInstanceOf(DateTimeExtractor::class);

    // Test some of the operator keys to ensure they are registered successfully
    expect($registry->operator('equals'))->toBeInstanceOf(EqualsOperator::class);
    expect($registry->operator('greater_than'))->toBeInstanceOf(GreaterThanOperator::class);
    expect($registry->operator('includes'))->toBeInstanceOf(ContainsOperator::class);
    expect($registry->operator('starts_with'))->toBeInstanceOf(StartsWithOperator::class);

    // Test the resolver key to ensure it is registered successfully
    expect($registry->resolver('system.config'))->toBeInstanceOf(ConfigResolver::class);

    // Test the evaluator key to ensure it is registered successfully
    expect($registry->evaluator('default'))->toBeInstanceOf(PredicateEvaluator::class);

    // Test some of the action keys to ensure they are registered successfully
    expect($registry->action('system.log'))->toBeInstanceOf(LogMessageAction::class);
    expect($registry->action('auth.logout'))->toBeInstanceOf(LogoutAction::class);
    expect($registry->action('web.redirect'))->toBeInstanceOf(RedirectAction::class);
});

test('it offers configuration and migration publishing in console', function () {
    // Fetch the publishable paths from Laravel's ServiceProvider base class
    $publishableConfig = ServiceProvider::pathsToPublish(
        LogicAsDataCoreServiceProvider::class, 
        'logic-as-data-config'
    );

    $publishableMigrations = ServiceProvider::pathsToPublish(
        LogicAsDataCoreServiceProvider::class, 
        'logic-as-data-migrations'
    );

    expect($publishableConfig)->not->toBeEmpty();
    expect($publishableMigrations)->not->toBeEmpty();
});

test('it registers artisan commands when running in console', function () {
    // Get all the registered commands from Artisan facade
    $commands = Artisan::all();

    // Check if the InstallCommand signature exists in the registered commands
    expect($commands)->toHaveKey('logic-as-data:install');
});
