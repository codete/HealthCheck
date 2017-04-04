<?php
declare(strict_types=1);

namespace Codete\HealthCheck\ResultHandler;

use Codete\HealthCheck\HealthCheck;
use Codete\HealthCheck\HealthStatus;
use Codete\HealthCheck\ResultHandler;

class NullHandler implements ResultHandler
{
    public function handle(HealthCheck $check, HealthStatus $result): void
    {
        // NOP
    }
}
