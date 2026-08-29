<?php

namespace AdminPlatform\Support;

class ModuleRegistry
{
    protected array $modules;

    public function __construct()
    {
        $this->modules = config('admin-platform.modules', []);
    }

    public function all(): array
    {
        return collect($this->modules)
            ->map(function ($module, $key) {

                return [
                    'name' => $key,
                    'label' => $module['label'] ?? $key,
                    'icon' => $module['icon'] ?? null,
                    'route' => $module['route'] ?? null,
                ];

            })
            ->values()
            ->toArray();
    }
}
