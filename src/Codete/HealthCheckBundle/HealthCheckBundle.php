<?php
declare(strict_types=1);

namespace Codete\HealthCheckBundle;

use Codete\HealthCheckBundle\DependencyInjection\Compiler\HealthCheckCompilerPass;
use Codete\HealthCheckBundle\DependencyInjection\Compiler\ResultHandlerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HealthCheckBundle extends Bundle
{
    /**
     * @inheritdoc
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new HealthCheckCompilerPass());
        $container->addCompilerPass(new ResultHandlerCompilerPass());
    }
}
