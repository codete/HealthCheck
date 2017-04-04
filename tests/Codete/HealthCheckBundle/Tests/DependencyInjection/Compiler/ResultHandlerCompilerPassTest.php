<?php

namespace Codete\HealthCheckBundle\Tests\DependencyInjection\Compiler;

use Codete\HealthCheckBundle\DependencyInjection\Compiler\ResultHandlerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ResultHandlerCompilerPassTest extends \PHPUnit\Framework\TestCase
{
    public function testProcess()
    {
        $registry = new Definition();
        $container = new ContainerBuilder();
        $container->setDefinition('hc.result_handler_registry', $registry);
        $container->setDefinition('some.result_handler', (new Definition())->addTag('hc.result_handler', ['id' => 'some']));
        $container->setDefinition('other.result_handler', (new Definition())->addTag('hc.result_handler', ['id' => 'other']));
        $container->setDefinition('hc.result_handler.no_alias', (new Definition())->addTag('hc.result_handler', ['id' => 'no_alias']));

        $pass = new ResultHandlerCompilerPass();
        $pass->process($container);

        $methodCalls = $registry->getMethodCalls();
        $this->assertCount(3, $methodCalls);
        $this->assertEquals(['register', ['some.result_handler', 'some']], $methodCalls[0]);
        $this->assertEquals(['register', ['other.result_handler', 'other']], $methodCalls[1]);
        $this->assertEquals(['register', ['hc.result_handler.no_alias', 'no_alias']], $methodCalls[2]);
        $this->assertTrue($container->hasAlias('hc.result_handler.some'));
        $this->assertTrue($container->hasAlias('hc.result_handler.other'));
        $this->assertFalse($container->hasAlias('hc.result_handler.no_alias'));
    }
}