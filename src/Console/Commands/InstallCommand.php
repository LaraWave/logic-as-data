<?php

namespace LaraWave\LogicAsData\Console\Commands;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logic-as-data:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Logic As Data package and publish its Service Provider';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->line('<fg=cyan>Installing Logic As Data Engine...</>');

        // Publish the configuration file
        $this->publishConfiguration();

        // Publish the dedicated App-level Service Provider
        $this->publishServiceProvider();

        // Publish the migration files
        $this->publishMigrations();

        $this->line('<fg=green>Logic As Data has been installed successfully!</>');

        // Provide crystal-clear next steps for the developer
        $this->showNextSteps();

        return self::SUCCESS;
    }

    /**
     * Publish the package configuration file.
     */
    private function publishConfiguration(): void
    {
        $configPath = app()->configPath('logic-as-data.php');

        if (file_exists($configPath)) {
            $this->line('  <fg=yellow>Config file already present in your app. SKIPPED !</>');
        } else {
            $this->callSilent('vendor:publish', [
                '--tag' => 'logic-as-data-config',
            ]);
            $this->line('  <fg=green>Config file published.</>');
        }
    }

    /**
     * Copy LogicAsDataServiceProvider into the host application's app/Providers directory.
     */
    private function publishServiceProvider(): void
    {
        $destinationPath = app()->path('Providers/LogicAsDataServiceProvider.php');

        // If the file already exists in host app, skip it
        if (File::exists($destinationPath)) {
            $this->line('  <fg=yellow>Service Provider already present in your app. SKIPPED !</>');
            return;
        }

        Artisan::call('vendor:publish', [
            '--tag' => 'logic-as-data-provider'
        ]);

        $this->line('  <fg=green>Service Provider published.</>');
    }

    /**
     * Publish the migration file.
     */
    private function publishMigrations(): void
    {
        $migrations = [
            'create_logic_rules_table.php',
            'create_logic_telemetry_table.php',
            'create_logic_traces_table.php',
        ];

        $path = app()->databasePath('migrations' . DIRECTORY_SEPARATOR);
        $missingMigrations = false;

        foreach ($migrations as $migration) {
            $files = File::glob($path . '*_' . $migration);

            if (! empty($files)) {
                $this->line("  <fg=yellow>Migration [{$migration}] already exists. SKIPPED!</>");
            } else {
                $missingMigrations = true;
            }
        }

        if ($missingMigrations) {
            $this->callSilent('vendor:publish', [
                '--tag' => 'logic-as-data-migrations',
            ]);
            $this->info('  <fg=green>Missing migration files successfully published.</>');
        }

        if ($this->confirm('Would you like to run the package migrations now?', true)) {
            $migratedAny = false;

            foreach ($migrations as $migration) {
                $files = File::glob($path . '*_' . $migration);

                if (! empty($files)) {
                    $migrationPath = str_replace(app()->basePath(DIRECTORY_SEPARATOR), '', head($files));

                    $this->call('migrate', ['--path' => $migrationPath]);
                    $migratedAny = true;
                }
            }

            if (! $migratedAny) {
                $this->warn('  <fg=yellow>No migration files found to run. SKIPPED!</>');
            } else {
                $this->info('  <fg=green>All package migrations executed.</>');
            }
        }
    }

    /**
     * Check if migration file already exists in host app.
     */
    private function migrationExists(string $file): bool
    {
        $path = app()->databasePath('migrations' . DIRECTORY_SEPARATOR);
        $files = File::glob($path . '*_' . $file);
        return count($files) > 0;
    }

    /**
     * Display the manual steps the developer needs to take to finish the installation.
     */
    private function showNextSteps(): void
    {
        $this->newLine();
        $this->line('<bg=yellow;fg=red> Action Required: </>');

        $this->line('  <fg=cyan>Please register the new service provider in your application.</>');
        $this->newLine();

        $this->line('   For <options=bold>Laravel 11+</>: Add it to <options=bold>bootstrap/providers.php</>');
        $this->line('   <fg=gray>App\Providers\LogicAsDataServiceProvider::class,</>');

        $this->newLine();

        $this->line('   For <options=bold>Laravel 10</>: Add it to the <options=bold>providers</> array in <options=bold>config/app.php</>');
        $this->line('   <fg=gray>App\Providers\LogicAsDataServiceProvider::class,</>');
        $this->newLine();
    }
}
