<?php

namespace Codete\HealthCheck\Tests\ResultHandler;

use Codete\HealthCheck\HealthCheck;
use Codete\HealthCheck\HealthStatus;
use Codete\HealthCheck\ResultHandler;
use Codete\HealthCheck\ResultHandler\Delegating;

class DelegatingTest extends \PHPUnit\Framework\TestCase
{
    public function testSuccess()
    {
        $check = $this->createMock(HealthCheck::class);
        $result = new HealthStatus(HealthStatus::OK, 'Cool');

        $success = $this->createMock(ResultHandler::class);
        $success->expects($this->once())->method('handle')->with($check, $result);
        $warning = $this->createMock(ResultHandler::class);
        $warning->expects($this->never())->method('handle');
        $error = $this->createMock(ResultHandler::class);
        $error->expects($this->never())->method('handle');

        (new Delegating($success, $warning, $error))->handle($check, $result);
    }

    public function testWarning()
    {
        $check = $this->createMock(HealthCheck::class);
        $result = new HealthStatus(HealthStatus::WARNING, 'Uh oh');

        $success = $this->createMock(ResultHandler::class);
        $success->expects($this->never())->method('handle');
        $warning = $this->createMock(ResultHandler::class);
        $warning->expects($this->once())->method('handle')->with($check, $result);
        $error = $this->createMock(ResultHandler::class);
        $error->expects($this->never())->method('handle');

        (new Delegating($success, $warning, $error))->handle($check, $result);
    }

    public function testError()
    {
        $check = $this->createMock(HealthCheck::class);
        $result = new HealthStatus(HealthStatus::ERROR, 'Aaaaaa');

        $success = $this->createMock(ResultHandler::class);
        $success->expects($this->never())->method('handle');
        $warning = $this->createMock(ResultHandler::class);
        $warning->expects($this->never())->method('handle');
        $error = $this->createMock(ResultHandler::class);
        $error->expects($this->once())->method('handle')->with($check, $result);

        (new Delegating($success, $warning, $error))->handle($check, $result);
    }
}
