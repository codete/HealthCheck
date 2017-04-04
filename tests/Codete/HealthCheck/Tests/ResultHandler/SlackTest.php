<?php

namespace Codete\HealthCheck\Tests\ResultHandler;

use Codete\HealthCheck\HealthCheck;
use Codete\HealthCheck\HealthStatus;
use Codete\HealthCheck\ResultHandler\Slack;
use Codete\HealthCheck\Tests\StubbedHealthCheck;
use Maknz\Slack\Client;
use Maknz\Slack\Message;

class SlackTest extends \PHPUnit\Framework\TestCase
{
    /**
     * This test is a little bit hacky but thanks to this whole integration is really thin.
     */
    public function testIntegration()
    {
        $handler = new Slack('https://slack.com', 'dev', 'notifier', ':ghost:');

        $ro = new \ReflectionObject($handler);
        $rp = $ro->getProperty('slack');
        $rp->setAccessible(true);
        /** @var Client $slack */
        $slack = $rp->getValue($handler);
        $this->assertSame('https://slack.com', $slack->getEndpoint());
        $this->assertSame('dev', $slack->getDefaultChannel());
        $this->assertSame('notifier', $slack->getDefaultUsername());
        $this->assertSame(':ghost:', $slack->getDefaultIcon());

        $message = $this->createMock(Message::class);
        $message->expects($this->once())->method('setText')->with('Test: OK!');
        $mock = $this->createMock(Client::class);
        $mock->expects($this->once())->method('createMessage')->willReturn($message);
        $mock->expects($this->once())->method('sendMessage')->with($message);
        $rp->setValue($handler, $mock);

        $check = $this->createMock(HealthCheck::class);
        $check->method('getName')->willReturn('Test');
        $status = new HealthStatus(HealthStatus::OK, 'OK!');
        $handler->handle($check, $status);
    }
}
