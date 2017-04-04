<?php

namespace Codete\HealthCheck\ResultHandler;

use Codete\HealthCheck\HealthCheck;
use Codete\HealthCheck\HealthStatus;
use Codete\HealthCheck\ResultHandler;
use Maknz\Slack\Client;

class Slack implements ResultHandler
{
    /**
     * @var Client
     */
    private $slack;

    /**
     * @param string $url
     * @param string $channel
     * @param string $username
     * @param string $icon
     */
    public function __construct(string $url, string $channel, string $username, string $icon)
    {
        $this->slack = new Client($url, [
            'channel' => $channel,
            'username' => $username,
            'icon' => $icon,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function handle(HealthCheck $check, HealthStatus $result): void
    {
        $message = $this->slack->createMessage();
        $message->setText(sprintf("%s: %s", $check->getName(), $result));
        $this->slack->sendMessage($message);
    }
}
