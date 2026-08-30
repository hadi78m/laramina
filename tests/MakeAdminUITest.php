<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class MakeAdminUITest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
    }

    /** @test */
    public function it_shows_error_for_nonexistent_model(): void
    {
        $exitCode = Artisan::call('laramina:make-ui', ['model' => 'NonExistent']);
        $output   = Artisan::output();

        $this->assertStringContainsString('Model not found', $output);
    }

    /** @test */
    public function it_can_resolve_model_namespace(): void
    {
        $exitCode = Artisan::call('laramina:make-ui', ['model' => 'User', '--force' => true]);
        $output   = Artisan::output();

        $this->assertStringNotContainsString('Model not found', $output);
        $this->assertStringContainsString('Admin UI generated', $output);
    }

    /** @test */
    public function it_generates_module_files(): void
    {
        Artisan::call('laramina:make-ui', ['model' => 'User', '--force' => true]);

        $modulePath = public_path('js/modules/users');
        $this->assertDirectoryExists($modulePath);
        $this->assertFileExists("{$modulePath}/table.js");
        $this->assertFileExists("{$modulePath}/forms/create-form.js");
        $this->assertFileExists("{$modulePath}/actions.js");
        $this->assertFileExists("{$modulePath}/module.js");
    }

    /** @test */
    public function it_generates_blade_view(): void
    {
        Artisan::call('laramina:make-ui', ['model' => 'User', '--force' => true]);

        $viewPath = resource_path('views/users/index.blade.php');
        $this->assertFileExists($viewPath);

        $content = File::get($viewPath);
        $this->assertStringContainsString('data-module="users"', $content);
    }

    /** @test */
    public function it_registers_module_in_config(): void
    {
        \Illuminate\Support\Facades\Config::set('laramina.modules', []);

        Artisan::call('laramina:make-ui', ['model' => 'User', '--force' => true]);

        $configFile = config_path('laramina.php');
        $this->assertFileExists($configFile);

        $config = include $configFile;
        $this->assertArrayHasKey('users', $config['modules']);
    }

    /** @test */
    public function it_respects_force_flag(): void
    {
        Artisan::call('laramina:make-ui', ['model' => 'User', '--force' => true]);
        $firstTime = File::lastModified(public_path('js/modules/users/table.js'));

        sleep(1);

        Artisan::call('laramina:make-ui', ['model' => 'User']);
        $secondTime = File::lastModified(public_path('js/modules/users/table.js'));

        $this->assertEquals($firstTime, $secondTime);
    }

    /** @test */
    public function it_overwrites_with_force_flag(): void
    {
        Artisan::call('laramina:make-ui', ['model' => 'User', '--force' => true]);
        $firstContent = File::get(public_path('js/modules/users/table.js'));

        Artisan::call('laramina:make-ui', ['model' => 'User', '--force' => true]);
        $secondContent = File::get(public_path('js/modules/users/table.js'));

        $this->assertEquals($firstContent, $secondContent);
    }

    /** @test */
    public function it_handles_fully_qualified_model_name(): void
    {
        Artisan::call('laramina:make-ui', ['model' => 'App\\Models\\User', '--force' => true]);
        $output = Artisan::output();

        $this->assertStringContainsString('Admin UI generated', $output);
    }

    /** @test */
    public function generated_table_js_contains_expected_structure(): void
    {
        Artisan::call('laramina:make-ui', ['model' => 'User', '--force' => true]);

        $tableJs = File::get(public_path('js/modules/users/table.js'));

        $this->assertStringContainsString('export default', $tableJs);
        $this->assertStringContainsString('endpoint:', $tableJs);
        $this->assertStringContainsString('search:', $tableJs);
        $this->assertStringContainsString('columns:', $tableJs);
        $this->assertStringContainsString('filters:', $tableJs);
        $this->assertStringContainsString('modals:', $tableJs);
    }

    /** @test */
    public function generated_form_js_has_create_and_edit(): void
    {
        Artisan::call('laramina:make-ui', ['model' => 'User', '--force' => true]);

        $formJs = File::get(public_path('js/modules/users/forms/create-form.js'));

        $this->assertStringContainsString('export const createForm', $formJs);
        $this->assertStringContainsString('export const editForm', $formJs);
        $this->assertStringContainsString('endpoint:', $formJs);
        $this->assertStringContainsString('updateEndpoint:', $formJs);
        $this->assertStringContainsString('deleteEndpoint:', $formJs);
    }

    /** @test */
    public function generated_actions_js_has_set_toggle(): void
    {
        Artisan::call('laramina:make-ui', ['model' => 'User', '--force' => true]);

        $actionsJs = File::get(public_path('js/modules/users/actions.js'));

        $this->assertStringContainsString('export const', $actionsJs);
        $this->assertStringContainsString('setToggle', $actionsJs);
        $this->assertStringContainsString('edit:', $actionsJs);
        $this->assertStringContainsString('delete:', $actionsJs);
    }
}
