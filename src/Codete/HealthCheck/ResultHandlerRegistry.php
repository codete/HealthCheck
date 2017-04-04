<?php
declare(strict_types=1);

namespace Codete\HealthCheck;

class ResultHandlerRegistry
{
    /**
     * @var ResultHandler[]
     */
    private $handlers = [];

    /**
     * Gets registered ResultHandler.
     *
     * @param string $name
     * @return ResultHandler
     */
    public function get($name): ResultHandler
    {
        if (empty($this->handlers[$name])) {
            throw new \InvalidArgumentException(sprintf('Handler "%s" was not registered.', $name));
        }
        return $this->handlers[$name];
    }

    /**
     * Registers a ResultHandler under given name.
     *
     * @param ResultHandler $handler
     * @param string $name
     */
    public function register(ResultHandler $handler, $name): void
    {
        if (isset($this->handlers[$name])) {
            throw new \InvalidArgumentException(sprintf(
                'Handler "%s" was already registered for %s',
                $name,
                get_class($this->handlers[$name])
            ));
        }
        $this->handlers[$name] = $handler;
    }
}
