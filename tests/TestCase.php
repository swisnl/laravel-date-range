<?php

namespace Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase as Orchestra;

use function Orchestra\Testbench\package_path;
use function Orchestra\Testbench\workbench_path;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        /* @phpstan-ignore-next-line */
        Factory::guessFactoryNamesUsing(function (string $modelName): string {
            $modelName = Str::after($modelName, '\\Models\\');

            return 'Workbench\\Database\\Factories\\'.$modelName.'Factory';
        });

        /* @phpstan-ignore-next-line */
        Factory::guessModelNamesUsing(function (Factory $factory): string {
            $modelName = Str::after($factory::class, '\\Factories\\');
            $modelName = Str::before($modelName, 'Factory');

            return 'Workbench\\App\\Models\\'.$modelName;
        });
    }

    public function resolveApplication()
    {
        $app = parent::resolveApplication();

        $app->useEnvironmentPath(package_path());

        return $app;
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(workbench_path('database/migrations'));
    }
}
