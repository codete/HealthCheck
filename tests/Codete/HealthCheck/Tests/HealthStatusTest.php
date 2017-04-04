<?php

namespace Codete\HealthCheck\Tests;

use Codete\HealthCheck\HealthStatus;

class HealthStatusTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideToString
     */
    public function testToString(HealthStatus $status, $expected)
    {
        $this->assertSame($expected, (string) $status);
    }

    public function provideToString()
    {
        yield [new HealthStatus(HealthStatus::WARNING, ''), ''];
        yield [new HealthStatus(HealthStatus::OK, 'Well done!'), 'Well done!'];
    }

    public function testWithStatus()
    {
        $status = new HealthStatus(HealthStatus::OK, '\o/');
        $new = $status->withStatus(HealthStatus::WARNING);

        $this->assertSame(HealthStatus::WARNING, $new->getStatus());
        $this->assertSame('\o/', $new->getMessage());
    }

    public function testWithMessage()
    {
        $status = new HealthStatus(HealthStatus::OK, '\o/');
        $new = $status->withMessage('changed');

        $this->assertSame(HealthStatus::OK, $new->getStatus());
        $this->assertSame('changed', $new->getMessage());
    }
}
