<?php

namespace Codete\HealthCheckBundle\Tests;

use Codete\HealthCheck\ResultHandler;
use Codete\HealthCheck\ResultHandler\Delegating;
use Codete\HealthCheck\ResultHandler\NullHandler;
use Codete\HealthCheck\ResultHandlerRegistry;
use Codete\HealthCheckBundle\DelegatingResultHandlerFactory;

class DelegatingResultHandlerFactoryTest extends \PHPUnit\Framework\TestCase
{
    /** @var ResultHandlerRegistry */
    private $registry;

    /** @var DelegatingResultHandlerFactory */
    private $factory;

    public function setUp()
    {
        $this->registry = new ResultHandlerRegistry();
        $this->factory = new DelegatingResultHandlerFactory($this->registry);
    }

    public function testNullHandlersAreCreatedInPlaceOfNulls()
    {
        $this->inspect(
            $this->factory->create(null, null, null),
            NullHandler::class,
            NullHandler::class,
            NullHandler::class
        );
    }

    public function testCreatingHandler()
    {
        $this->registry->register($this->createMock(ResultHandler::class), 'success');
        $this->registry->register($this->createMock(ResultHandler::class), 'warning');
        $this->registry->register($this->createMock(ResultHandler::class), 'error');

        $this->inspect(
            $this->factory->create('success', 'warning', 'error'),
            $this->registry->get('success'),
            $this->registry->get('warning'),
            $this->registry->get('error')
        );
    }

    private function inspect(Delegating $handler, $success, $warning, $error)
    {
        $ro = new \ReflectionObject($handler);
        foreach (['success', 'warning', 'error'] as $prop) {
            $rp = $ro->getProperty($prop);
            $rp->setAccessible(true);
            if (is_object($$prop)) {
                $this->assertSame($$prop, $rp->getValue($handler));
            } else {
                $this->assertInstanceOf($$prop, $rp->getValue($handler));
            }
        }
    }
}
