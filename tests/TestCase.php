<?php

namespace TobMoeller\LaravelMailMiddleware\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use TobMoeller\LaravelMailMiddleware\LaravelMailMiddlewareServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'TobMoeller\\LaravelMailMiddleware\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelMailMiddlewareServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('mail.default', 'array');
        config()->set('mail.mailers.array.transport', 'array');
        config()->set('queue.default', 'sync');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-mail-middleware_table.php.stub';
        $migration->up();
        */
    }
}
