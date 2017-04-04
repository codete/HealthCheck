<?php

namespace Codete\HealthCheck\Tests\ResultHandler;

use Codete\HealthCheck\HealthCheck;
use Codete\HealthCheck\HealthStatus;
use Codete\HealthCheck\ResultHandler\Remembering;

class RememberingTest extends \PHPUnit\Framework\TestCase
{
    /** @var Remembering */
    private $handler;

    public function setUp()
    {
        $this->handler = new Remembering();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage "Foo" check has not been ran yet.
     */
    public function testThrowsExceptionWhenNotRan()
    {
        $hc = $this->createMock(HealthCheck::class);
        $hc->method('getName')->willReturn('Foo');
        $this->handler->getResultFor($hc);
    }

    public function testGetResult()
    {
        $status = new HealthStatus(HealthStatus::OK, 'OK!');
        $hc = $this->createMock(HealthCheck::class);
        $hc->method('check')->willReturn($status);

        $this->handler->handle($hc, $status);
        $this->assertSame($status, $this->handler->getResultFor($hc));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage "Foo" check has not been ran yet.
     */
    public function testClear()
    {
        $status = new HealthStatus(HealthStatus::OK, 'OK!');
        $hc = $this->createMock(HealthCheck::class);
        $hc->method('getName')->willReturn('Foo');
        $hc->method('check')->willReturn($status);

        $this->handler->handle($hc, $status);
        $this->handler->clear();
        $this->handler->getResultFor($hc);
    }
}
