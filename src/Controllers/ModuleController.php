<?php

namespace Laramina\Controllers;

use App\Http\Controllers\Controller;
use Laramina\Services\ModuleService;

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
