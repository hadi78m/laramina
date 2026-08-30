<?php

namespace Tests;

use Illuminate\Support\Facades\Route;
use Laramina\Controllers\ModuleController;
use Laramina\Services\ModuleService;
use Laramina\Support\ModuleRegistry;

class ModuleControllerTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated(): void
    {
        $registry = new ModuleRegistry();
        $service = new ModuleService($registry);
        $controller = new ModuleController($service);

        $this->assertInstanceOf(ModuleController::class, $controller);
    }

    /** @test */
    public function index_returns_json_response(): void
    {
        Route::get('/api/laramina/modules', [ModuleController::class, 'index']);

        $response = $this->getJson('/api/laramina/modules');

        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $response->assertJsonStructure([
            '*' => ['name', 'label', 'icon', 'route'],
        ]);
    }

    /** @test */
    public function index_returns_module_names(): void
    {
        Route::get('/api/laramina/modules', [ModuleController::class, 'index']);

        $response = $this->getJson('/api/laramina/modules');
        $names = collect($response->json())->pluck('name')->toArray();

        $this->assertContains('users', $names);
        $this->assertContains('posts', $names);
    }
}
