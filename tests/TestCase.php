<?php

namespace Bernskiold\LaravelBlame\Tests;

use Bernskiold\LaravelBlame\LaravelBlameServiceProvider;
use Bernskiold\LaravelBlame\Support\Blame;
use Bernskiold\LaravelBlame\Tests\Models\User;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Blame::resolveUserIdUsing(null);

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->artisan('migrate')->run();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelBlameServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        config()->set('auth.providers.users.model', User::class);
    }
}
