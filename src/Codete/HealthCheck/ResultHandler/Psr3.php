<?php
declare(strict_types=1);

namespace Codete\HealthCheck\ResultHandler;

use Codete\HealthCheck\HealthCheck;
use Codete\HealthCheck\HealthStatus;
use Codete\HealthCheck\ResultHandler;
use Psr\Log\LoggerInterface;

class Psr3 implements ResultHandler
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $level;

    /**
     * @param LoggerInterface $logger
     * @param string $level
     */
    public function __construct(LoggerInterface $logger, string $level)
    {
        $this->logger = $logger;
        $this->level = $level;
    }

    public function handle(HealthCheck $check, HealthStatus $result): void
    {
        $this->logger->log($this->level, sprintf("%s: %s", $check->getName(), $result));
    }
}
