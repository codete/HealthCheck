<?php

namespace Codete\HealthCheckBundle\Tests\Command;

use Codete\HealthCheck\HealthCheck;
use Codete\HealthCheck\HealthCheckRegistry;
use Codete\HealthCheck\HealthStatus;
use Codete\HealthCheck\ResultHandler;
use Codete\HealthCheckBundle\Command\RunAllCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\Container;

class RunAllCommandTest extends \PHPUnit\Framework\TestCase
{
    public function testExecute()
    {
        $registry = new HealthCheckRegistry();
        $registry->register(new StubbedHealthCheck('Imfine', new HealthStatus(HealthStatus::OK, '')));
        $registry->register(new StubbedHealthCheck('Warning', new HealthStatus(HealthStatus::WARNING, 'Heads up!')));
        $registry->register(new StubbedHealthCheck('Erroring', new HealthStatus(HealthStatus::ERROR, 'Oh noes')));

        $handler = $this->createMock(ResultHandler::class);
        // 3 checks and all 3 calls are not lost due to handler manipulations
        $handler->expects($this->exactly(3))->method('handle');

        $container = $this->createMock(Container::class);
        $container->method('get')->will($this->returnCallback(function ($id) use ($registry, $handler) {
            switch ($id) {
                case 'hc.health_check_registry':
                    return $registry;
                case 'hc.delegating_result_handler':
                    return $handler;
                default:
                    throw new \InvalidArgumentException('Stub me! ' . $id);
            }
        }));

        $command = new RunAllCommand();
        $command->setContainer($container);

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $expectedOutput = <<<OUTPUT
👌 Imfine
W Warning - Heads up!
E Erroring - Oh noes

OUTPUT;
        $this->assertSame($expectedOutput, $commandTester->getDisplay());
    }
}

class StubbedHealthCheck implements HealthCheck
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var HealthStatus
     */
    private $result;

    public function __construct($name, HealthStatus $result)
    {
        $this->name = $name;
        $this->result = $result;
    }

    /**
     * @inheritdoc
     */
    public function check(): HealthStatus
    {
        return $this->result;
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return $this->name;
    }
}
