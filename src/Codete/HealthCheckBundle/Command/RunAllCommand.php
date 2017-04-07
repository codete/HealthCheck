<?php
declare(strict_types=1);

namespace Codete\HealthCheckBundle\Command;

use Codete\HealthCheck\Executor;
use Codete\HealthCheck\HealthCheckRegistry;
use Codete\HealthCheck\ResultHandler;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunAllCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('health-check:run-all')
            ->setDescription('Execute all defined health checks and show their results.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $handler = new PrettyOutputWritingResultHandler($output);
        /** @var Executor $executor */
        $executor = $this->getContainer()->get('hc.executor');
        /** @var HealthCheckRegistry $registry */
        $registry = $this->getContainer()->get('hc.health_check_registry');
        foreach ($registry->getAll() as $healthCheck) {
            $result = $executor->run($healthCheck);
            $handler->handle($healthCheck, $result);
        }
    }
}
