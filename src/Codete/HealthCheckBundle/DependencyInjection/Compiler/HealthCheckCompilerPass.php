<?php
declare(strict_types=1);

namespace Codete\HealthCheckBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class HealthCheckCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        if (! $container->hasDefinition('hc.health_check_registry')) {
            return;
        }
        $definition = $container->getDefinition('hc.health_check_registry');
        foreach ($container->findTaggedServiceIds('hc.health_check') as $id => $tags) {
            $definition->addMethodCall('register', [new Reference($id)]);
        }
    }
}
