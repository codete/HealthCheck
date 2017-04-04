<?php
declare(strict_types=1);

namespace Codete\HealthCheck;

class HealthStatus
{
    const OK = 0;
    const WARNING = 1;
    const ERROR = 2;

    /**
     * @var int
     */
    private $status;

    /**
     * @var string
     */
    private $message;

    /**
     * @param int $status
     * @param string $message
     */
    public function __construct(int $status, string $message)
    {
        if (! in_array($status, [self::OK, self::WARNING, self::ERROR])) {
            throw new \InvalidArgumentException(sprintf("%d is not a valid status"));
        }
        $this->status = $status;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getMessage();
    }

    /**
     * Gets check result, either self::OK, self::WARNING or self::ERROR.
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Gets status message.
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Creates new HealthStatus with same status but different message.
     *
     * @param string $message
     * @return HealthStatus
     */
    public function withMessage($message): HealthStatus
    {
        return new HealthStatus($this->status, $message);
    }

    /**
     * Creates new HealthStatus with same message but different status.
     *
     * @param int $status
     * @return HealthStatus
     */
    public function withStatus($status): HealthStatus
    {
        return new HealthStatus($status, $this->message);
    }
}
