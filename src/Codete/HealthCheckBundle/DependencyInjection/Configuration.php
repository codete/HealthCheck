<?php
declare(strict_types=1);

namespace Codete\HealthCheckBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('healthcheck');

        $rootNode
            ->fixXmlConfig('handler')
            ->children()
                ->arrayNode('status')
                    ->isRequired()
                    ->children()
                        ->scalarNode('green')->isRequired()->end()
                        ->scalarNode('yellow')->isRequired()->end()
                        ->scalarNode('red')->isRequired()->end()
                    ->end()
                ->end()
                ->arrayNode('handlers')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                    ->children()
                            ->scalarNode('type')
                                ->isRequired()
                                ->treatNullLike('null')
                                ->beforeNormalization()
                                    ->always()
                                    ->then(function ($v) { return strtolower($v); })
                                ->end()
                            ->end()
                            ->scalarNode('id')->end() // for anything that references another service
                            ->scalarNode('level')->end() // psr3
                            ->arrayNode('members') // chain
                                ->canBeUnset()
                                ->performNoDeepMerging()
                                ->prototype('scalar')->end()
                            ->end()
                            ->scalarNode('url')->end()      // slack
                            ->scalarNode('channel')->end()  // slack
                            ->scalarNode('username')->end() // slack
                            ->scalarNode('icon')->end()     // slack
                    ->end()
                    ->validate()
                        ->always(function (array $check) {
                            return $this->prepareSpecificConfigurations($check);
                        })
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    private function prepareSpecificConfigurations(array $h)
    {
        switch ($h['type']) {
            case 'psr3':
                return $this->extract($h, ['id', 'level']);
            case 'chain':
                return $this->extract($h, ['members']);
            case 'remembering':
                return ['type' => $h['type']];
            case 'slack':
                return $this->extract($h, ['url', 'channel', 'username', 'icon']);
            default:
                throw new \InvalidArgumentException(sprintf('Unknown handler type "%s".', $h['type']));
        }
    }

    private function extract(array $h, array $keys)
    {
        $subset = ['type' => $h['type']];
        foreach ($keys as $k) {
            if (! isset($h[$k])) {
                throw new \InvalidArgumentException(sprintf(
                    '"%s" setting is required for handler of type "%s"',
                    $k,
                    $h['type']
                ));
            }
            $subset[$k] = $h[$k];
        }
        return $subset;
    }
}
