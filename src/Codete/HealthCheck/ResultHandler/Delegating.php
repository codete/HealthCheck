<?php
declare(strict_types=1);

namespace Codete\HealthCheck\ResultHandler;

use Codete\HealthCheck\HealthCheck;
use Codete\HealthCheck\HealthStatus;
use Codete\HealthCheck\ResultHandler;

class Delegating implements ResultHandler
{
    /**
     * @var ResultHandler
     */
    private $success;

    /**
     * @var ResultHandler
     */
    private $warning;

    /**
     * @var ResultHandler
     */
    private $error;

    public function __construct(ResultHandler $success, ResultHandler $warning, ResultHandler $error)
    {
        $this->success = $success;
        $this->warning = $warning;
        $this->error = $error;
    }

    /**
     * @inheritdoc
     */
    public function handle(HealthCheck $check, HealthStatus $result): void
    {
        switch ($result->getStatus()) {
            case HealthStatus::OK:
                $this->success->handle($check, $result);
                break;
            case HealthStatus::WARNING:
                $this->warning->handle($check, $result);
                break;
            case HealthStatus::ERROR:
                $this->error->handle($check, $result);
                break;
        }
    }
}
