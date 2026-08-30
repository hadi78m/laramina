<?php

namespace Tests;

use Laramina\Services\ModuleService;

class ModuleServiceTest extends TestCase
{
    /** @test */
    public function it_returns_modules_from_registry(): void
    {
        $service = new ModuleService(new \Laramina\Support\ModuleRegistry());
        $modules = $service->getModules();

        $this->assertIsArray($modules);
        $this->assertArrayHasKey('users', array_column($modules, 'name') + array_flip(array_column($modules, 'name')));
        // Check structure
        $usersModule = collect($modules)->firstWhere('name', 'users');
        $this->assertNotNull($usersModule);
    }

    /** @test */
    public function it_delegates_to_registry(): void
    {
        $service = new ModuleService(new \Laramina\Support\ModuleRegistry());
        $modules = $service->getModules();

        // Should have same count as config modules
        $configModules = config('laramina.modules');
        $this->assertCount(count($configModules), $modules);
    }
}
