<?php
declare(strict_types=1);

namespace Codete\HealthCheck;

class Executor
{
    /**
     * @var HealthCheckRegistry
     */
    private $registry;

    /**
     * @var ResultHandler
     */
    private $resultHandler;

    /**
     * @param HealthCheckRegistry $registry
     * @param ResultHandler $resultHandler
     */
    public function __construct(HealthCheckRegistry $registry, ResultHandler $resultHandler)
    {
        $this->registry = $registry;
        $this->resultHandler = $resultHandler;
    }

    /**
     * Runs all registered health checks.
     */
    public function runAll(): void
    {
        foreach ($this->registry->getAll() as $check) {
            $this->run($check);
        }
    }

    /**
     * Runs given check, propagates its result to result handler and returns it.
     *
     * @param HealthCheck $check
     * @return HealthStatus
     */
    public function run(HealthCheck $check): HealthStatus
    {
        $result = $check->check();
        if ($this->shouldStatusChangeDueToExpiration($result, $check->validUntil())) {
            $result = $result->withStatus(HealthStatus::WARNING);
        }
        $this->resultHandler->handle($check, $result);
        return $result;
    }

    private function shouldStatusChangeDueToExpiration(HealthStatus $status, \DateTimeImmutable $validUntil = null): bool
    {
        if ($status->getStatus() !== HealthStatus::OK || $validUntil === null) {
            return false;
        }
        return $validUntil <= new \DateTimeImmutable();
    }
}
