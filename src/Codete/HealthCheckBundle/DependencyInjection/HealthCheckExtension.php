<?php
declare(strict_types=1);

namespace Codete\HealthCheckBundle\DependencyInjection;

use Codete\HealthCheck\ResultHandler\Chain;
use Codete\HealthCheck\ResultHandler\Psr3;
use Codete\HealthCheck\ResultHandler\Remembering;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class HealthCheckExtension extends Extension
{
    /**
     * @inheritdoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $container->setParameter('hc.status.green', $config['status']['green']);
        $container->setParameter('hc.status.yellow', $config['status']['yellow']);
        $container->setParameter('hc.status.red', $config['status']['red']);

        foreach ($config['handlers'] as $id => $config) {
            $def = $this->createHandler($config);
            $def->addTag('hc.result_handler', ['id' => $id]);
            $container->setDefinition($this->handlerId($id), $def);
        }
    }

    /**
     * @param $config
     * @return Definition
     */
    private function createHandler($config)
    {
        switch ($config['type']) {
            case 'psr3':
                return new Definition(Psr3::class, [new Reference($config['id']), $config['level']]);
            case 'chain':
                $def = new Definition(Chain::class);
                foreach ($config['members'] as $member) {
                    $def->addArgument(new Reference($this->handlerId($member)));
                }
                return $def;
            case 'remembering':
                return new Definition(Remembering::class);
        }
    }

    private function handlerId($id)
    {
        return sprintf('hc.result_handler.%s', $id);
    }
}
