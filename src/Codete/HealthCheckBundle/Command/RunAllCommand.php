<?php
declare(strict_types=1);

namespace Codete\HealthCheckBundle\Command;

use Codete\HealthCheck\Executor;
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
        // to not interfere with any defined handlers we'll create new executor
        $handler = $this->getContainer()->get('hc.delegating_result_handler');
        $executor = new Executor(
            $this->getContainer()->get('hc.health_check_registry'),
            new ResultHandler\Chain(new PrettyOutputWritingResultHandler($output), $handler)
        );

        $executor->runAll();
    }
}
