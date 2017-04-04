<?php

namespace Codete\HealthCheckBundle\Tests\DependencyInjection\Compiler;

use Codete\HealthCheckBundle\DependencyInjection\Compiler\HealthCheckCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class HealthCheckCompilerPassTest extends \PHPUnit\Framework\TestCase
{
    public function testProcess()
    {
        $registry = new Definition();
        $container = new ContainerBuilder();
        $container->setDefinition('hc.health_check_registry', $registry);
        $container->setDefinition('some.health_check', (new Definition())->addTag('hc.health_check'));
        $container->setDefinition('other.health_check', (new Definition())->addTag('hc.health_check'));

        $pass = new HealthCheckCompilerPass();
        $pass->process($container);

        $methodCalls = $registry->getMethodCalls();
        $this->assertCount(2, $methodCalls);
        $this->assertEquals(['register', ['some.health_check']], $methodCalls[0]);
        $this->assertEquals(['register', ['other.health_check']], $methodCalls[1]);
    }
}
