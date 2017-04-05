<?php

namespace Codete\HealthCheck\Tests;

use Codete\HealthCheck\Executor;
use Codete\HealthCheck\ExpiringHealthCheck;
use Codete\HealthCheck\HealthCheck;
use Codete\HealthCheck\HealthCheckRegistry;
use Codete\HealthCheck\HealthStatus;
use Codete\HealthCheck\ResultHandler;

class ExecutorTest extends \PHPUnit\Framework\TestCase
{
    private $healthCheck;

    private $healthCheckRegistry;

    public function setUp()
    {
        $this->healthCheck = $this->createMock(ExpiringHealthCheck::class);

        $this->healthCheckRegistry = new HealthCheckRegistry();
        $this->healthCheckRegistry->register($this->healthCheck);
    }

    public function testSimpleFlow()
    {
        $this->healthCheckRegistry = new HealthCheckRegistry();

        $result = new HealthStatus(HealthStatus::OK, 'OK');
        $this->healthCheck = $this->createMock(HealthCheck::class);
        $this->healthCheck->expects($this->once())->method('check')->willReturn($result);
        $this->healthCheckRegistry->register($this->healthCheck);
        $anotherHealthCheck = $this->createMock(HealthCheck::class);
        $anotherHealthCheck->expects($this->once())->method('check')->willReturn($result);
        $this->healthCheckRegistry->register($anotherHealthCheck);
        $handler = $this->getHandler($result->getStatus(), $result->getMessage());

        (new Executor($this->healthCheckRegistry, $handler))
            ->runAll();
        $this->assertSame(2, $handler->called);
    }

    public function testGreenOutdatedCheckTurnsYellow()
    {
        $result = new HealthStatus(HealthStatus::OK, 'OK');
        $this->healthCheck->expects($this->once())->method('check')->willReturn($result);
        $this->healthCheck->expects($this->any())->method('validUntil')
            ->willReturn((new \DateTimeImmutable())->modify('-1 hour'));

        (new Executor($this->healthCheckRegistry, $this->getHandler(HealthStatus::WARNING, $result->getMessage())))
            ->runAll();
    }

    public function testRedOutdatedCheckDoesNotTurnYellow()
    {
        $result = new HealthStatus(HealthStatus::ERROR, 'Not OK');
        $this->healthCheck->expects($this->once())->method('check')->willReturn($result);
        $this->healthCheck->expects($this->any())->method('validUntil')
            ->willReturn((new \DateTimeImmutable())->modify('-1 hour'));

        (new Executor($this->healthCheckRegistry, $this->getHandler(HealthStatus::ERROR, $result->getMessage())))
            ->runAll();
    }

    private function getHandler(int $expectedStatus, string $expectedMessage): ExecutorTestResultHandler
    {
        return new ExecutorTestResultHandler($this, $expectedStatus, $expectedMessage);
    }
}

class ExecutorTestResultHandler implements ResultHandler
{
    /**
     * @var \PHPUnit\Framework\TestCase
     */
    private $phpunit;

    /**
     * @var int
     */
    private $expectedStatus;

    /**
     * @var string
     */
    private $expectedMessage;

    /**
     * @var int
     */
    public $called = 0;

    public function __construct(\PHPUnit\Framework\TestCase $phpunit, int $expectedStatus, string $expectedMessage)
    {
        $this->phpunit = $phpunit;
        $this->expectedStatus = $expectedStatus;
        $this->expectedMessage = $expectedMessage;
    }

    public function handle(HealthCheck $check, HealthStatus $result): void
    {
        ++$this->called;
        $this->phpunit->assertSame($this->expectedStatus, $result->getStatus());
        $this->phpunit->assertSame($this->expectedMessage, $result->getMessage());
    }
}
