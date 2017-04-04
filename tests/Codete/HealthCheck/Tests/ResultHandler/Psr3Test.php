<?php

namespace Codete\HealthCheck\Tests\ResultHandler;

use Codete\HealthCheck\HealthCheck;
use Codete\HealthCheck\HealthStatus;
use Codete\HealthCheck\ResultHandler\Psr3;
use Psr\Log\LoggerInterface;

class Psr3Test extends \PHPUnit\Framework\TestCase
{
    public function testHandle()
    {
        $psr3 = $this->createMock(LoggerInterface::class);
        $psr3->expects($this->once())->method('log')->with(
            $this->matches('critical'),
            $this->isType('string')
        );

        $handler = new Psr3($psr3, 'critical');
        $handler->handle(
            $this->createMock(HealthCheck::class),
            new HealthStatus(HealthStatus::OK, 'OK!')
        );
    }
}
