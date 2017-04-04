<?php
declare(strict_types=1);

namespace Codete\HealthCheck\ResultHandler;

use Codete\HealthCheck\HealthCheck;
use Codete\HealthCheck\HealthStatus;
use Codete\HealthCheck\ResultHandler;

class Chain implements ResultHandler
{
    /**
     * @var ResultHandler[]
     */
    private $handlers = [];

    public function __construct(ResultHandler ...$handlers)
    {
        $this->handlers = $handlers;
    }

    /**
     * @inheritdoc
     */
    public function handle(HealthCheck $check, HealthStatus $result): void
    {
        foreach ($this->handlers as $handler) {
            $handler->handle($check, $result);
        }
    }
}
