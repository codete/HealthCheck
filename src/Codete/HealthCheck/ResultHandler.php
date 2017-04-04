<?php
declare(strict_types=1);

namespace Codete\HealthCheck;

interface ResultHandler
{
    /**
     * Method is called after check is executed and its result is known.
     *
     * @param HealthCheck $check
     * @param HealthStatus $result
     * @return void
     */
    public function handle(HealthCheck $check, HealthStatus $result): void;
}
