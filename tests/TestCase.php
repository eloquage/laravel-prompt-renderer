<?php

namespace Eloquage\LaravelPromptRenderer\Tests;

use Eloquage\LaravelPromptRenderer\LaravelPromptRendererServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelPromptRendererServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');

        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('cache.default', 'array');
        $app['config']->set('cache.stores.array', [
            'driver' => 'array',
            'serialize' => false,
        ]);
    }

    protected function runPromptTemplatesMigration(): void
    {
        $migration = include dirname(__DIR__).'/database/migrations/create_prompt_templates_table.php.stub';

        $migration->up();
    }
}
