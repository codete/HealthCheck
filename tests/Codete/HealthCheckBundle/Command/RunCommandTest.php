<?php

namespace Codete\HealthCheckBundle\Tests\Command;

use Codete\HealthCheck\Executor;
use Codete\HealthCheck\HealthCheck;
use Codete\HealthCheck\HealthCheckRegistry;
use Codete\HealthCheck\HealthStatus;
use Codete\HealthCheck\ResultHandler;
use Codete\HealthCheckBundle\Command\RunCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\Container;

class RunCommandTest extends \PHPUnit\Framework\TestCase
{
    public function testExecute()
    {
        $registry = new HealthCheckRegistry();
        $registry->register(new HealthCheckToRun());

        $handler = $this->createMock(ResultHandler::class);
        $handler->expects($this->exactly(1))->method('handle');

        $executor = new Executor($registry, $handler);

        $container = $this->createMock(Container::class);
        $container->method('get')->will($this->returnCallback(function ($id) use ($registry, $executor) {
            switch ($id) {
                case 'hc.health_check_registry':
                    return $registry;
                case 'hc.executor':
                    return $executor;
                default:
                    throw new \InvalidArgumentException('Stub me! ' . $id);
            }
        }));

        $command = new RunCommand();
        $command->setContainer($container);

        $commandTester = new CommandTester($command);
        $commandTester->execute(['FQCN' => HealthCheckToRun::class]);

        $expectedOutput = <<<OUTPUT
Heads up!

OUTPUT;
        $this->assertSame($expectedOutput, $commandTester->getDisplay());
        $this->assertSame(HealthStatus::WARNING, $commandTester->getStatusCode());
    }
}

class HealthCheckToRun implements HealthCheck
{
    /**
     * @inheritdoc
     */
    public function check(): HealthStatus
    {
        return new HealthStatus(HealthStatus::WARNING, 'Heads up!');
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'A check';
    }

    /**
     * @inheritdoc
     */
    public function validUntil(): ?\DateTimeImmutable
    {
        return null;
    }
}
