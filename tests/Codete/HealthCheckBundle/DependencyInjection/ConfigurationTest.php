<?php

namespace Codete\HealthCheckBundle\Tests\DependencyInjection;

use Codete\HealthCheckBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

class ConfigurationTest extends \PHPUnit\Framework\TestCase
{
    public function testFullConfiguration()
    {
        $yaml = Yaml::parse(file_get_contents(__DIR__ . '/fixtures/full.yml'));

        $processor = new Processor();
        $configuration = new Configuration();
        $parsed = $processor->processConfiguration($configuration, $yaml);

        $expected = [
            'handlers' => [
                'psr3' => [
                    'type' => 'psr3',
                    'id' => 'logger',
                    'level' => 'warning',
                ],
                'chain' => [
                    'type' => 'chain',
                    'members' => ['psr3', 'elephpant'],
                ],
                'elephpant' => [
                    'type' => 'remembering',
                ],
            ],
            'status' => [
                'green' => null,
                'yellow' => 'elephpant',
                'red' => 'chain',
            ]
        ];
        $this->assertEquals($expected, $parsed);
    }
}
