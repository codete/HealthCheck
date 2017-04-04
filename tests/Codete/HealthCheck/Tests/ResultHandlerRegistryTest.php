<?php

namespace Codete\HealthCheck\Tests;

use Codete\HealthCheck\ResultHandler;
use Codete\HealthCheck\ResultHandlerRegistry;


class ResultHandlerRegistryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ResultHandlerRegistry
     */
    private $registry;

    public function setUp()
    {
        $this->registry = new ResultHandlerRegistry();
    }

    public function testRegister()
    {
        $handler = $this->createMock(ResultHandler::class);
        $this->registry->register($handler, 'foo');
        $this->assertSame($handler, $this->registry->get('foo'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Handler "foo" was not registered.
     */
    public function testGetThrowsExceptionOnMiss()
    {
        $this->registry->get('foo');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Handler "foo" was already registered for
     */
    public function testCantOverwriteHandler()
    {
        $handler = $this->createMock(ResultHandler::class);
        $this->registry->register($handler, 'foo');
        $this->registry->register($handler, 'foo');
    }
}
