<?php

namespace Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Laramina\LaraminaServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaraminaServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('laramina.modules', [
            'users' => [
                'label' => 'کاربران',
                'icon'  => 'fas fa-users',
                'route' => 'users.index',
            ],
            'posts' => [
                'label' => 'مقالات',
                'icon'  => 'fas fa-file-alt',
                'route' => 'posts.index',
            ],
        ]);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
    }
}
