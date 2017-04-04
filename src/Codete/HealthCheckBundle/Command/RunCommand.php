<?php
declare(strict_types=1);

namespace Codete\HealthCheckBundle\Command;

use Codete\HealthCheck\Executor;
use Codete\HealthCheck\HealthCheckRegistry;
use Codete\HealthCheck\HealthStatus;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('health-check:run')
            ->setDescription('Execute single health check and output its result in format suitable for Nagios.')
            ->addArgument('FQCN', InputArgument::REQUIRED, 'Fully-Qualified Class Name of a health check to execute.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var HealthCheckRegistry $registry */
        $registry = $this->getContainer()->get('hc.health_check_registry');
        $check = null;
        foreach ($registry->getAll() as $healthCheck) {
            if (get_class($healthCheck) === $input->getArgument('FQCN')) {
                $check = $healthCheck;
                break;
            }
        }
        if ($check === null) {
            throw new \InvalidArgumentException(sprintf(
                '%s is not a registered health check. Have you forgotten to tag it with "%s"?',
                $input->getArgument('FQCN'),
                'hc.health_check'
            ));
        }

        /** @var Executor $executor */
        $executor = $this->getContainer()->get('hc.executor');
        $result = $executor->run($check);
        $output->writeln((string) $result);

        return $result->getStatus() === HealthStatus::OK ? 0 : 1;
    }
}
