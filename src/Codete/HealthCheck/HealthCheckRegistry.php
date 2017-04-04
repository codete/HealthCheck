<?php
declare(strict_types=1);

namespace Codete\HealthCheck;

class HealthCheckRegistry
{
    /**
     * @var HealthCheck[]
     */
    private $checks = [];

    /**
     * Gets all registered health checks.
     * 
     * @return HealthCheck[]
     */
    public function getAll()
    {
        return $this->checks;
    }

    /**
     * Registers a health check.
     * 
     * @param HealthCheck $check
     */
    public function register(HealthCheck $check): void
    {
        $this->checks[spl_object_hash($check)] = $check;
    }
}
