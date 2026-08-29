<?php

namespace AdminPlatform\Controllers;

use App\Http\Controllers\Controller;
use AdminPlatform\Services\ModuleService;

class ModuleController extends Controller
{
    protected ModuleService $service;

    public function __construct(ModuleService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json(
            $this->service->getModules()
        );
    }
}
