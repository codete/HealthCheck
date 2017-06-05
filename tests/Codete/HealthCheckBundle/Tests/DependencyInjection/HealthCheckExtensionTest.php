<?php

namespace Codete\HealthCheckBundle\Tests\DependencyInjection;

use Codete\HealthCheck\ResultHandler\Chain;
use Codete\HealthCheck\ResultHandler\Psr3;
use Codete\HealthCheck\ResultHandler\Remembering;
use Codete\HealthCheckBundle\DependencyInjection\HealthCheckExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Yaml\Yaml;

class HealthCheckExtensionTest extends \PHPUnit\Framework\TestCase
{
    public function testLoad()
    {
        $container = $this->getContainer();
        $loader = new HealthCheckExtension();
        $container->registerExtension($loader);
        $loader->load(Yaml::parse(file_get_contents(__DIR__.'/fixtures/full.yml')), $container);

        // parameters
        $this->assertSame(null, $container->getParameter('hc.status.green'));
        $this->assertSame('elephpant', $container->getParameter('hc.status.yellow'));
        $this->assertSame('chain', $container->getParameter('hc.status.red'));

        // handlers

        // psr3
        $this->assertTrue($container->hasDefinition('hc.result_handler.psr3'));
        $def = $container->getDefinition('hc.result_handler.psr3');
        $this->assertSame(Psr3::class, $def->getClass());
        $this->assertEquals([new Reference('logger'), 'warning'], $def->getArguments());
        $this->assertSame([['id' => 'psr3']], $def->getTag('hc.result_handler'));

        // chain
        $this->assertTrue($container->hasDefinition('hc.result_handler.chain'));
        $def = $container->getDefinition('hc.result_handler.chain');
        $this->assertSame(Chain::class, $def->getClass());
        $this->assertEquals([
            new Reference('hc.result_handler.psr3'),
            new Reference('hc.result_handler.elephpant'),
        ], $def->getArguments());
        $this->assertSame([['id' => 'chain']], $def->getTag('hc.result_handler'));

        // remembering
        $this->assertTrue($container->hasDefinition('hc.result_handler.elephpant'));
        $def = $container->getDefinition('hc.result_handler.elephpant');
        $this->assertSame(Remembering::class, $def->getClass());
        $this->assertSame([['id' => 'elephpant']], $def->getTag('hc.result_handler'));
    }

    public function testServiceRegistration()
    {
        $container = $this->getContainer();
        $loader = new HealthCheckExtension();
        $container->registerExtension($loader);
        $loader->load(Yaml::parse(file_get_contents(__DIR__.'/fixtures/full.yml')), $container);

        // old way
        $this->assertTrue($container->has('hc.executor'));
        $this->assertTrue($container->has('hc.health_check_registry'));
        $this->assertTrue($container->has('hc.result_handler_registry'));
        $this->assertTrue($container->has('hc.delegating_result_handler_factory'));
        $this->assertTrue($container->has('hc.delegating_result_handler'));

        // FQCN way
        $this->assertTrue($container->has(\Codete\HealthCheck\Executor::class));
        $this->assertTrue($container->has(\Codete\HealthCheck\HealthCheckRegistry::class));
        $this->assertTrue($container->has(\Codete\HealthCheck\ResultHandlerRegistry::class));
        $this->assertTrue($container->has(\Codete\HealthCheckBundle\DelegatingResultHandlerFactory::class));
        $this->assertTrue($container->has(\Codete\HealthCheck\ResultHandler\Delegating::class));
    }

    private function getContainer()
    {
        return new ContainerBuilder(new ParameterBag([
            'kernel.bundles'          => [],
            'kernel.cache_dir'        => __DIR__,
            'kernel.compiled_classes' => [],
            'kernel.debug'            => false,
            'kernel.environment'      => 'test',
            'kernel.name'             => 'kernel',
            'kernel.root_dir'         => __DIR__,
        ]));
    }
}
