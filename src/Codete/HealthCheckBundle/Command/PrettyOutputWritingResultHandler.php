<?php
declare(strict_types=1);

namespace Codete\HealthCheckBundle\Command;

use Codete\HealthCheck\HealthCheck;
use Codete\HealthCheck\HealthStatus;
use Codete\HealthCheck\ResultHandler;
use Symfony\Component\Console\Output\OutputInterface;

class PrettyOutputWritingResultHandler implements ResultHandler
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @inheritdoc
     */
    public function handle(HealthCheck $check, HealthStatus $result): void
    {
        $this->output->writeln(sprintf(
            '<fg=%s>%s</> %s%s',
            $this->getColor($result),
            $this->getSymbol($result),
            $check->getName(),
            $result->getMessage() ? (' - ' . $result->getMessage()) : ''
        ));
    }

    private function getColor(HealthStatus $result): string
    {
        switch ($result->getStatus()) {
            case HealthStatus::OK:
                return 'green';
            case HealthStatus::WARNING:
                return 'yellow';
            case HealthStatus::ERROR:
                return 'red';
        }
    }

    private function getSymbol(HealthStatus $result): string
    {
        switch ($result->getStatus()) {
            case HealthStatus::OK:
                return "\xF0\x9F\x91\x8C";
            case HealthStatus::WARNING:
                return "W";
            case HealthStatus::ERROR:
                return "E";
        }
    }
}
