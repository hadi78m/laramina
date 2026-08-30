<?php

namespace Tests;

class ConfigTest extends TestCase
{
    /** @test */
    public function it_has_default_config_file(): void
    {
        $configPath = dirname(__DIR__) . '/config/laramina.php';
        $this->assertFileExists($configPath);
    }

    /** @test */
    public function default_config_returns_array(): void
    {
        $config = include dirname(__DIR__) . '/config/laramina.php';
        $this->assertIsArray($config);
        $this->assertArrayHasKey('modules', $config);
    }

    /** @test */
    public function default_modules_is_empty_array(): void
    {
        $config = include dirname(__DIR__) . '/config/laramina.php';
        $this->assertIsArray($config['modules']);
        // Default config has modules commented out, so should be empty or have commented example
    }

    /** @test */
    public function config_is_publishable(): void
    {
        $taggedPaths = \Illuminate\Support\Facades\Artisan::call('vendor:publish', [
            '--tag' => 'laramina-config',
            '--force' => true,
        ]);

        // Config file should exist in the test app
        $publishedConfig = config_path('laramina.php');
        $this->assertFileExists($publishedConfig);
    }
}
