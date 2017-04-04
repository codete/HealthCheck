<?php

namespace Codete\HealthCheck\Tests;

use Codete\HealthCheck\HealthCheck;
use Codete\HealthCheck\HealthCheckRegistry;

class HealthCheckRegistryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var HealthCheckRegistry
     */
    private $registry;

    public function setUp()
    {
        $this->registry = new HealthCheckRegistry();
    }

    public function testRegister()
    {
        $check = $this->createMock(HealthCheck::class);
        $this->registry->register($check);
        $another = $this->createMock(HealthCheck::class);
        $this->registry->register($another);
        $this->assertSame([$check, $another], array_values($this->registry->getAll()));
    }

    public function testRegisteringSameInstanceTwiceIsIgnored()
    {
        $check = $this->createMock(HealthCheck::class);
        $this->registry->register($check);
        $this->registry->register($check);
        $this->assertCount(1, $this->registry->getAll());
    }
}
