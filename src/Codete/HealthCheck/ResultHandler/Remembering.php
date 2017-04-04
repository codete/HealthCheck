<?php
declare(strict_types=1);

namespace Codete\HealthCheck\ResultHandler;

use Codete\HealthCheck\HealthCheck;
use Codete\HealthCheck\HealthStatus;
use Codete\HealthCheck\ResultHandler;

class Remembering implements ResultHandler
{
    /**
     * @var HealthStatus[]
     */
    private $memory = [];

    /**
     * Clears remembered results.
     */
    public function clear()
    {
        $this->memory = [];
    }

    /**
     * Gets result for specific health check.
     *
     * @param HealthCheck $check
     * @return HealthStatus
     * @throws \RuntimeException if check has not been ran yet
     */
    public function getResultFor(HealthCheck $check)
    {
        $oid = spl_object_hash($check);
        if (empty($this->memory[$oid])) {
            throw new \RuntimeException(sprintf('"%s" check has not been ran yet.', $check->getName()));
        }
        return $this->memory[$oid];
    }

    /**
     * @inheritdoc
     */
    public function handle(HealthCheck $check, HealthStatus $result): void
    {
        $this->memory[spl_object_hash($check)] = $result;
    }
}
