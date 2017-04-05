<?php
declare(strict_types=1);

namespace Codete\HealthCheck;

interface HealthCheck
{
    /**
     * Performs an arbitrary check.
     *
     * @return HealthStatus
     */
    public function check(): HealthStatus;

    /**
     * Gets human readable name of the check.
     *
     * @return string
     */
    public function getName(): string;
}
