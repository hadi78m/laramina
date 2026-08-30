<?php

namespace Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

class ServiceProviderTest extends TestCase
{
    /** @test */
    public function service_provider_is_registered(): void
    {
        $providers = $this->app->getLoadedProviders();
        $this->assertArrayHasKey(
            \Laramina\LaraminaServiceProvider::class,
            $providers
        );
    }

    /** @test */
    public function config_is_merged(): void
    {
        $modules = Config::get('laramina.modules');
        $this->assertIsArray($modules);
        $this->assertArrayHasKey('users', $modules);
        $this->assertArrayHasKey('posts', $modules);
    }

    /** @test */
    public function config_module_has_correct_structure(): void
    {
        $userModule = Config::get('laramina.modules.users');
        $this->assertEquals('کاربران', $userModule['label']);
        $this->assertEquals('fas fa-users', $userModule['icon']);
        $this->assertEquals('users.index', $userModule['route']);
    }

    /** @test */
    public function translations_are_loaded(): void
    {
        $enTranslation = trans('laramina::adminUI.common.name', [], 'en');
        $this->assertEquals('Name', $enTranslation);
    }

    /** @test */
    public function persian_translations_are_loaded(): void
    {
        $faTranslation = trans('laramina::adminUI.common.name', [], 'fa');
        $this->assertEquals('نام', $faTranslation);
    }

    /** @test */
    public function views_are_registered(): void
    {
        $viewFactory = view();
        $this->assertTrue($viewFactory->exists('laramina::adminPlatform'));
    }

    /** @test */
    public function artisan_commands_are_registered(): void
    {
        $commands = Artisan::all();
        $this->assertArrayHasKey('laramina:make-ui', $commands);
    }

    /** @test */
    public function config_can_be_overridden(): void
    {
        Config::set('laramina.modules.custom', [
            'label' => 'سفارشی',
            'icon'  => 'fas fa-cog',
            'route' => 'custom.index',
        ]);

        $modules = Config::get('laramina.modules');
        $this->assertArrayHasKey('custom', $modules);
        $this->assertEquals('سفارشی', $modules['custom']['label']);
    }
}
