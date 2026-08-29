<?php

namespace AdminPlatform\Services;

use AdminPlatform\Support\ModuleRegistry;

class ModuleService
{
    protected ModuleRegistry $registry;

    public function __construct(ModuleRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function getModules(): array
    {
        return $this->registry->all();
    }
}
