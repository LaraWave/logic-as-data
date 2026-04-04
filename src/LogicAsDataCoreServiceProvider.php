<?php

namespace LaraWave\LogicAsData;

use LaraWave\LogicAsData\Operators\GreaterThanOrEqualOperator;
use LaraWave\LogicAsData\Extractors\DateTimeExtractor;
use LaraWave\LogicAsData\Extractors\ConfigExtractor;
use LaraWave\LogicAsData\Extractors\UserExtractor;
use LaraWave\LogicAsData\Operators\DoesNotContainOperator;
use LaraWave\LogicAsData\Operators\GreaterThanOperator;
use LaraWave\LogicAsData\Operators\IsNotEmptyOperator;
use LaraWave\LogicAsData\Operators\IsNotNullOperator;
use LaraWave\LogicAsData\Operators\LessThanOperator;
use LaraWave\LogicAsData\Operators\ContainsOperator;
use LaraWave\LogicAsData\Operators\EndsWithOperator;
use LaraWave\LogicAsData\Operators\HasAllOfOperator;
use LaraWave\LogicAsData\Operators\HasAnyOfOperator;
use LaraWave\LogicAsData\Operators\HasNoneOfOperator;
use LaraWave\LogicAsData\Operators\InArrayOperator;
use LaraWave\LogicAsData\Operators\IsEmptyOperator;
use LaraWave\LogicAsData\Operators\IsNullOperator;
use LaraWave\LogicAsData\Operators\EqualsOperator;
use LaraWave\LogicAsData\Operators\LessThanOrEqualOperator;
use LaraWave\LogicAsData\Operators\NotEqualsOperator;
use LaraWave\LogicAsData\Operators\NotInArrayOperator;
use LaraWave\LogicAsData\Operators\StartsWithOperator;
use LaraWave\LogicAsData\Operators\BetweenOperator;
use LaraWave\LogicAsData\Operators\MatchesRegexOperator;
use LaraWave\LogicAsData\Evaluators\PredicateEvaluator;
use LaraWave\LogicAsData\Console\Commands\InstallCommand;
use LaraWave\LogicAsData\Observers\LogicRuleObserver;
use LaraWave\LogicAsData\Resolvers\DateTimeResolver;
use LaraWave\LogicAsData\Resolvers\LiteralResolver;
use LaraWave\LogicAsData\Resolvers\ConfigResolver;
use LaraWave\LogicAsData\Actions\LogMessageAction;
use LaraWave\LogicAsData\Actions\RedirectAction;
use LaraWave\LogicAsData\Actions\LogoutAction;
use LaraWave\LogicAsData\Models\LogicRule;
use LaraWave\LogicAsData\LogicRegistry;
use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Gate;
use LaraWave\LogicAsData\LogicEngine;

class LogicAsDataCoreServiceProvider extends ServiceProvider
{
    /**
     * All of the container singletons that should be registered.
     *
     * @var array<string, string>
     */
    public array $singletons = [
        LogicRegistry::class => LogicRegistry::class,
        LogicEngine::class   => LogicEngine::class,
    ];

    /**
     * Map source aliases to their Source Extractor classes.
     *
     * @var array<string, string>
     */
    private array $extractors = [
        // Auth/User Extractors
        'user.id'           => UserExtractor::class,
        'user.name'         => UserExtractor::class,
        'user.email'        => UserExtractor::class,
        'user.is_verified'  => UserExtractor::class,
        'user.roles'        => UserExtractor::class,

        // Date, Time Extractors
        'date.current'      => DateTimeExtractor::class,
        'date.day_name'     => DateTimeExtractor::class,
        'date.month_name'   => DateTimeExtractor::class,
        'date.year'         => DateTimeExtractor::class,
        'date.month'        => DateTimeExtractor::class,
        'date.day'          => DateTimeExtractor::class,
        'date.is_weekday'   => DateTimeExtractor::class,
        'date.is_weekend'   => DateTimeExtractor::class,
        'date.is_today'     => DateTimeExtractor::class,
        'date.is_future'    => DateTimeExtractor::class,
        'date.is_past'      => DateTimeExtractor::class,
        'date.is_leap_year' => DateTimeExtractor::class,
        'time.current'      => DateTimeExtractor::class,
        'time.hour'         => DateTimeExtractor::class,
        'time.minute'       => DateTimeExtractor::class,
        'time.second'       => DateTimeExtractor::class,

        // Config Extractor
        'system.config'     => ConfigExtractor::class,
    ];

    /**
     * Map mathematical/logical operators to their classes.
     *
     * @var array<string, string>
     */
    private array $operators = [
        // Emptiness Checks
        'is_empty'              => IsEmptyOperator::class,
        'is_not_empty'          => IsNotEmptyOperator::class,
        'is_null'               => IsNullOperator::class,
        'is_not_null'           => IsNotNullOperator::class,

        // Equality Checks
        'equals'                => EqualsOperator::class,
        'not_equals'            => NotEqualsOperator::class,

        // Relational Checks (Math & Dates)
        'greater_than'          => GreaterThanOperator::class,
        'less_than'             => LessThanOperator::class,
        'greater_than_or_equal' => GreaterThanOrEqualOperator::class,
        'less_than_or_equal'    => LessThanOrEqualOperator::class,

        // String Checks
        'contains'              => ContainsOperator::class,
        'does_not_contain'      => DoesNotContainOperator::class,
        'starts_with'           => StartsWithOperator::class,
        'ends_with'             => EndsWithOperator::class,

        // Array / Lists Checks
        'in_array'              => InArrayOperator::class,
        'not_in_array'          => NotInArrayOperator::class,
        'has_any_of'            => HasAnyOfOperator::class,
        'has_all_of'            => HasAllOfOperator::class,
        'has_none_of'           => HasNoneOfOperator::class,
        'is_one_of'             => InArrayOperator::class,
        'includes'              => ContainsOperator::class,
        'is_not_one_of'         => NotInArrayOperator::class,
        'does_not_include'      => DoesNotContainOperator::class,

        // Others
        'between'               => BetweenOperator::class,
        'matches_regex'         => MatchesRegexOperator::class,
    ];

    /**
     * Map resolver aliases to their Target Resolver classes.
     *
     * @var array<string, string>
     */
    private array $resolvers = [
        'core.literal' => LiteralResolver::class,

        // Date, Time Resolver
        'date.current'      => DateTimeResolver::class,
        'date.day_name'     => DateTimeResolver::class,
        'date.month_name'   => DateTimeResolver::class,
        'date.year'         => DateTimeResolver::class,
        'date.month'        => DateTimeResolver::class,
        'date.day'          => DateTimeResolver::class,
        'date.is_weekday'   => DateTimeResolver::class,
        'date.is_weekend'   => DateTimeResolver::class,
        'date.is_today'     => DateTimeResolver::class,
        'date.is_future'    => DateTimeResolver::class,
        'date.is_past'      => DateTimeResolver::class,
        'date.is_leap_year' => DateTimeResolver::class,
        'time.current'      => DateTimeResolver::class,
        'time.hour'         => DateTimeResolver::class,
        'time.minute'       => DateTimeResolver::class,
        'time.second'       => DateTimeResolver::class,

        // Config Resolver
        'system.config' => ConfigResolver::class,
    ];

    /**
     * Map evaluator aliases to their Evaluator classes.
     *
     * @var array<string, string>
     */
    private array $evaluators = [
        'default' => PredicateEvaluator::class,
    ];

    /**
     * Map action aliases to their Action classes.
     *
     * @var array<string, string>
     */
    private array $actions = [
        'auth.logout' => LogoutAction::class,
        'system.log' => LogMessageAction::class,
        'web.redirect' => RedirectAction::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge the default package configuration with the application's published copy
        $this->mergeConfigFrom(__DIR__.'/../config/logic-as-data.php', 'logic-as-data');
    }

    /**
     * Bootstrap services.
     */
    public function boot(LogicRegistry $registry): void
    {
        if ($this->app->runningInConsole()) {
            $this->registerCommands();
            $this->offerPublishing();
        }

        // Register source extractor mappings defined in the extractors array
        foreach ($this->extractors as $key => $extractor) {
            $registry->registerExtractor($key, $extractor);
        }

        // Register operator mappings defined in the operators array
        foreach ($this->operators as $key => $operator) {
            $registry->registerOperator($key, $operator);
        }

        // Register resolver mappings defined in the resolvers array
        foreach ($this->resolvers as $key => $resolver) {
            $registry->registerResolver($key, $resolver);
        }

        // Register evaluator mappings defined in the evaluators array
        foreach ($this->evaluators as $key => $evaluator) {
            $registry->registerEvaluator($key, $evaluator);
        }

        // Register action mappings defined in the actions array
        foreach ($this->actions as $key => $action) {
            $registry->registerAction($key, $action);
        }

        // Load the Views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'logic-as-data');

        // Load the Routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Define the gate. By default, it returns false (deny all) 
        // unless the environment is local.
        Gate::define('viewLogicAsData', function ($user = null) {
            return app()->environment('local');
        });

        LogicRule::observe(LogicRuleObserver::class);
    }

    /**
     * Register the package's custom Artisan commands.
     */
    protected function registerCommands(): void
    {
        $this->commands([
            Console\Commands\InstallCommand::class,
        ]);
    }

    /**
     * Handle publishing of configuration and migrations.
     */
    private function offerPublishing(): void
    {
        // Allow developers to publish the config file using:
        // php artisan vendor:publish --tag="logic-as-data-config"
        $this->publishes([
            __DIR__.'/../config/logic-as-data.php' => config_path('logic-as-data.php'),
        ], 'logic-as-data-config');

        // Allow developers to publish the frontened assets using:
        // php artisan vendor:publish --tag="logic-as-data-assets"
        $this->publishes([
            __DIR__.'/../resources/dist' => public_path('vendor/logic-as-data'),
        ], 'logic-as-data-assets');

        // Allow developers to publish the database migration using:
        // php artisan vendor:publish --tag="logic-as-data-migrations"
        $this->publishes([
            __DIR__.'/../database/migrations/2026_03_21_053426_create_logic_rules_table.php' 
                => $this->getMigrationFileName('create_logic_rules_table.php', 0),
            __DIR__.'/../database/migrations/2026_04_02_172644_create_logic_telemetry_table.php' 
                => $this->getMigrationFileName('create_logic_telemetry_table.php', 1),
            __DIR__.'/../database/migrations/2026_04_03_060144_create_logic_traces_table.php' 
                => $this->getMigrationFileName('create_logic_traces_table.php', 2),
        ], 'logic-as-data-migrations');

        // Allow developers to publish the service provider using:
        // php artisan vendor:publish --tag="logic-as-data-provider"
        $this->publishes([
            __DIR__.'/../stubs/LogicAsDataServiceProvider.stub' => app_path('Providers/LogicAsDataServiceProvider.php'),
        ], 'logic-as-data-provider');
    }

    /**
     * Generate the migration filename.
     * Returns the existing migration file path or generates a new one.
     */
    private function getMigrationFileName(string $fileName, int $offset = 0): string
    {
        $timestamp = date('Y_m_d_His', time() + $offset);

        $fileSystem = $this->app->make(Filesystem::class);
        $path = $this->app->databasePath('migrations' . DIRECTORY_SEPARATOR);

        $existingFileName = collect($fileSystem->glob($path . '*_' . $fileName))->first();

        // If migration file exists, return the existing filename
        // If it doesn't exists, create a new filename
        return $existingFileName ?: "{$path}{$timestamp}_{$fileName}";
    }
}
