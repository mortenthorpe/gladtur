<?php

namespace UAParser\Tests\Result;

use UAParser\Result\DeviceResult;

/**
 * @author Benjamin Laugueux <benjamin@yzalis.com>
 */
class DeviceResultTest extends \PHPUnit_Framework_TestCase
{
    public function testFromArray()
    {
        $deviceResult = new DeviceResult();
        $deviceResult->fromArray(array(
            'constructor' => 'Apple',
            'model'      => 'iPhone',
            'type'       => 'mobile',
        ));

        $this->assertInstanceOf('UAParser\Result\DeviceResult', $deviceResult);
        $this->assertInstanceOf('UAParser\Result\DeviceResultInterface', $deviceResult);

        $this->assertEquals('Apple', $deviceResult->getConstructor());
        $this->assertInternalType('string', $deviceResult->getConstructor());

        $this->assertEquals('iPhone', $deviceResult->getModel());
        $this->assertInternalType('string', $deviceResult->getModel());

        $this->assertEquals('mobile', $deviceResult->getType());
        $this->assertInternalType('string', $deviceResult->getType());

        $this->assertEquals('Apple iPhone', $deviceResult->__toString());
        $this->assertInternalType('string', $deviceResult->__toString());

        $this->assertTrue($deviceResult->isMobile());
        $this->assertTrue($deviceResult->is('mobile'));
        $this->assertFalse($deviceResult->isTablet());
        $this->assertFalse($deviceResult->is('tablet'));
        $this->assertFalse($deviceResult->isDesktop());
        $this->assertFalse($deviceResult->is('desktop'));
        $this->assertFalse($deviceResult->is('foo'));
    }
}