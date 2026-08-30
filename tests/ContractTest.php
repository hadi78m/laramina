<?php

namespace Tests;

use Laramina\Contracts\AdminModule;

class ContractTest extends TestCase
{
    /** @test */
    public function it_has_admin_module_interface(): void
    {
        $interface = new \ReflectionClass(AdminModule::class);
        $this->assertTrue($interface->isInterface());
    }

    /** @test */
    public function admin_module_interface_requires_methods(): void
    {
        $interface = new \ReflectionClass(AdminModule::class);

        $expectedMethods = ['name', 'label', 'icon', 'route'];

        foreach ($expectedMethods as $method) {
            $this->assertTrue(
                $interface->hasMethod($method),
                "AdminModule interface is missing method: {$method}"
            );
        }
    }
}
