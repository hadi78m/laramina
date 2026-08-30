<?php

namespace Tests;

use Laramina\Support\ModuleRegistry;

class ModuleRegistryTest extends TestCase
{
    /** @test */
    public function it_returns_all_modules_from_config(): void
    {
        $registry = new ModuleRegistry();
        $modules = $registry->all();

        $this->assertIsArray($modules);
        $this->assertCount(2, $modules);
    }

    /** @test */
    public function it_transforms_config_modules_to_array(): void
    {
        $registry = new ModuleRegistry();
        $modules = $registry->all();

        $usersModule = collect($modules)->firstWhere('name', 'users');

        $this->assertNotNull($usersModule);
        $this->assertEquals('users', $usersModule['name']);
        $this->assertEquals('کاربران', $usersModule['label']);
        $this->assertEquals('fas fa-users', $usersModule['icon']);
        $this->assertEquals('users.index', $usersModule['route']);
    }

    /** @test */
    public function it_handles_string_modules(): void
    {
        // Temporarily override config with string module
        config(['laramina.modules' => [
            'simple-module',
        ]]);

        $registry = new ModuleRegistry();
        $modules = $registry->all();

        $this->assertCount(1, $modules);
        $this->assertEquals('simple-module', $modules[0]['name']);
        $this->assertEquals('simple-module', $modules[0]['label']);
        $this->assertNull($modules[0]['icon']);
        $this->assertNull($modules[0]['route']);
    }

    /** @test */
    public function it_handles_mixed_modules(): void
    {
        config(['laramina.modules' => [
            'string-module',
            'array-module' => [
                'label' => 'آرایه‌ای',
                'icon'  => 'fas fa-check',
                'route' => 'array.index',
            ],
        ]]);

        $registry = new ModuleRegistry();
        $modules = $registry->all();

        $this->assertCount(2, $modules);
    }

    /** @test */
    public function it_handles_empty_modules(): void
    {
        config(['laramina.modules' => []]);

        $registry = new ModuleRegistry();
        $modules = $registry->all();

        $this->assertIsArray($modules);
        $this->assertEmpty($modules);
    }

    /** @test */
    public function it_uses_key_as_name_for_array_modules(): void
    {
        config(['laramina.modules' => [
            'my-custom-module' => [
                'label' => 'سفارشی',
                'icon'  => 'fas fa-cog',
                'route' => 'custom.index',
            ],
        ]]);

        $registry = new ModuleRegistry();
        $modules = $registry->all();

        $this->assertEquals('my-custom-module', $modules[0]['name']);
    }

    /** @test */
    public function it_falls_back_to_key_for_missing_label(): void
    {
        config(['laramina.modules' => [
            'fallback' => [
                'icon'  => 'fas fa-box',
                'route' => 'fallback.index',
            ],
        ]]);

        $registry = new ModuleRegistry();
        $modules = $registry->all();

        $this->assertEquals('fallback', $modules[0]['label']);
    }
}
