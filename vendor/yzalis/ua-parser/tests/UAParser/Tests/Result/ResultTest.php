<?php

namespace UAParser\Tests\Result;

use UAParser\Result\ResultFactory;

/**
 * @author Benjamin Laugueux <benjamin@yzalis.com>
 */
class ResultTest  extends \PHPUnit_Framework_TestCase
{
    public function testNewInstance()
    {
        $factory = new ResultFactory();
        $result  = $factory->newInstance();

        $this->assertInstanceOf('UAParser\Result\Result', $result);
        $this->assertInstanceOf('UAParser\Result\ResultInterface', $result);
    }

    public function testCreateFromArray()
    {
        $factory = new ResultFactory();
        $result  = $factory->createFromArray(array(
            'browser' => array(
                'family' => 'Safari',
                'major'  => '6',
                'minor'  => '0',
                'patch'  => '2',
                'rendering_engine'  => 'WebKit',
            ),
            'operating_system' => array(
                'family' => 'Mac OS',
                'major'  => '10',
                'minor'  => '8',
                'patch'  => '4',
            ),
            'device' => array(
                'constructor' => 'Apple',
                'model'       => 'iPad',
                'type'        => 'tablet',
            ),
            'email_client' => array(
                'family' => 'Thunderbird',
                'major'  => '3',
                'minor'  => '1',
                'patch'  => '2',
            )
        ));

        $this->assertInstanceOf('UAParser\Result\Result', $result);
        $this->assertInstanceOf('UAParser\Result\ResultInterface', $result);

        $this->assertInstanceOf('UAParser\Result\BrowserResult', $result->getBrowser());
        $this->assertEquals('Safari', $result->getBrowser()->getFamily());
        $this->assertEquals(6, $result->getBrowser()->getMajor());
        $this->assertEquals(0, $result->getBrowser()->getMinor());
        $this->assertEquals(2, $result->getBrowser()->getPatch());
    
        $this->assertInstanceOf('UAParser\Result\OperatingSystemResult', $result->getOperatingSystem());
        $this->assertEquals('Mac OS', $result->getOperatingSystem()->getFamily());
        $this->assertEquals(10, $result->getOperatingSystem()->getMajor());
        $this->assertEquals(8, $result->getOperatingSystem()->getMinor());
        $this->assertEquals(4, $result->getOperatingSystem()->getPatch());
    
        $this->assertInstanceOf('UAParser\Result\EmailClientResult', $result->getEmailClient());
        $this->assertEquals('Thunderbird', $result->getEmailClient()->getFamily());
        $this->assertEquals(3, $result->getEmailClient()->getMajor());
        $this->assertEquals(1, $result->getEmailClient()->getMinor());
        $this->assertEquals(2, $result->getEmailClient()->getPatch());
    
        $this->assertInstanceOf('UAParser\Result\DeviceResult', $result->getDevice());
        $this->assertEquals('Apple', $result->getDevice()->getConstructor());
        $this->assertEquals('iPad', $result->getDevice()->getModel());
        $this->assertEquals('tablet', $result->getDevice()->getType());
    }
}