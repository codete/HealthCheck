<?php
declare(strict_types=1);

namespace Codete\HealthCheckBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ResultHandlerCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        if (! $container->hasDefinition('hc.result_handler_registry')) {
            return;
        }
        $definition = $container->getDefinition('hc.result_handler_registry');
        foreach ($container->findTaggedServiceIds('hc.result_handler') as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall(
                    'register',
                    [new Reference($id), $attributes['id']]
                );
                // do NOT create circular deps by aliasing service under same name
                if ($id !== $this->handlerId($attributes['id'])) {
                    $container->addAliases([$this->handlerId($attributes['id']) => $id]);
                }
            }
        }
    }

    private function handlerId($id)
    {
        return sprintf('hc.result_handler.%s', $id);
    }
}
