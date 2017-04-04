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
        yield [new HealthStatus(HealthStatus::OK, ''), '0'];
        yield [new HealthStatus(HealthStatus::WARNING, ''), '1'];
        yield [new HealthStatus(HealthStatus::ERROR, ''), '2'];
        yield [new HealthStatus(HealthStatus::OK, 'Well done!'), '0 Well done!'];
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
