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

    /**
     * Optional date to indicate until when the check is considered safe. Useful for 3rd party API health checks
     * when you know when used version will no longer be supported. In practice checks that are OK but valid
     * date is due will turn to warnings.
     *
     * @return \DateTimeImmutable|null
     */
    public function validUntil(): ?\DateTimeImmutable;
}
