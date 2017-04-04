<?php

namespace Codete\HealthCheck\Tests\ResultHandler;

use Codete\HealthCheck\HealthCheck;
use Codete\HealthCheck\HealthStatus;
use Codete\HealthCheck\ResultHandler;
use Codete\HealthCheck\ResultHandler\Chain;

class ChainTest extends \PHPUnit\Framework\TestCase
{
    public function testHandle()
    {
        $check = $this->createMock(HealthCheck::class);
        $result = new HealthStatus(HealthStatus::WARNING, 'Uh oh');

        $h1 = $this->createMock(ResultHandler::class);
        $h1->expects($this->once())->method('handle')->with($check, $result);
        $h2 = $this->createMock(ResultHandler::class);
        $h2->expects($this->once())->method('handle')->with($check, $result);

        (new Chain($h1, $h2))->handle($check, $result);
    }
}
