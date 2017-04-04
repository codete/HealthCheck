<?php
declare(strict_types=1);

namespace Codete\HealthCheckBundle;

use Codete\HealthCheck\ResultHandler;
use Codete\HealthCheck\ResultHandler\Delegating;
use Codete\HealthCheck\ResultHandler\NullHandler;
use Codete\HealthCheck\ResultHandlerRegistry;

class DelegatingResultHandlerFactory
{
    /**
     * @var ResultHandlerRegistry
     */
    private $registry;

    /**
     * @param ResultHandlerRegistry $registry
     */
    public function __construct(ResultHandlerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Creates new Delegating ResultHandler that delegates calls to chosen handlers (or NullHandler).
     *
     * @param null|string $success
     * @param null|string $warning
     * @param null|string $error
     * @return Delegating
     */
    public function create($success, $warning, $error)
    {
        return new Delegating(
            $success === null ? new NullHandler() : $this->registry->get($success),
            $warning === null ? new NullHandler() : $this->registry->get($warning),
            $error === null ? new NullHandler() : $this->registry->get($error)
        );
    }
}
