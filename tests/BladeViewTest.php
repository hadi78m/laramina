<?php

namespace Tests;

use Illuminate\Support\Facades\File;

class BladeViewTest extends TestCase
{
    /** @test */
    public function admin_platform_view_exists(): void
    {
        $viewPath = dirname(__DIR__) . '/resources/views/adminPlatform.blade.php';
        $this->assertFileExists($viewPath);
    }

    /** @test */
    public function blade_view_contains_app_div(): void
    {
        $content = File::get(dirname(__DIR__) . '/resources/views/adminPlatform.blade.php');
        $this->assertStringContainsString('id="app"', $content);
    }

    /** @test */
    public function blade_view_loads_sweetalert(): void
    {
        $content = File::get(dirname(__DIR__) . '/resources/views/adminPlatform.blade.php');
        $this->assertStringContainsString('sweetalert', $content);
    }

    /** @test */
    public function blade_view_loads_admin_lang_js(): void
    {
        $content = File::get(dirname(__DIR__) . '/resources/views/adminPlatform.blade.php');
        $this->assertStringContainsString('admin-lang.js', $content);
    }

    /** @test */
    public function blade_view_injects_admin_user_roles(): void
    {
        $content = File::get(dirname(__DIR__) . '/resources/views/adminPlatform.blade.php');
        $this->assertStringContainsString('window.AdminUser', $content);
    }

    /** @test */
    public function blade_view_injects_laravel_routes(): void
    {
        $content = File::get(dirname(__DIR__) . '/resources/views/adminPlatform.blade.php');
        $this->assertStringContainsString('window.LaravelRoutes', $content);
    }

    /** @test */
    public function blade_view_injects_translations(): void
    {
        $content = File::get(dirname(__DIR__) . '/resources/views/adminPlatform.blade.php');
        $this->assertStringContainsString('window.AdminLang', $content);
    }

    /** @test */
    public function blade_view_loads_laramina_bootstrap(): void
    {
        $content = File::get(dirname(__DIR__) . '/resources/views/adminPlatform.blade.php');
        $this->assertStringContainsString('laramina.js', $content);
    }
}
