<?php

namespace UAParser\Tests\Result;

use UAParser\Result\OperatingSystemResult;

/**
 * @author Benjamin Laugueux <benjamin@yzalis.com>
 */
class OperatingSystemResultTest extends \PHPUnit_Framework_TestCase
{
    public function testFromArray()
    {
        $operatingSystemResult = new OperatingSystemResult();
        $operatingSystemResult->fromArray(array(
            'family' => 'Mac OSX',
            'major'  => '10',
            'minor'  => '8',
            'patch'  => '3',
        ));

        $this->assertInstanceOf('UAParser\Result\OperatingSystemResult', $operatingSystemResult);
        $this->assertInstanceOf('UAParser\Result\OperatingSystemResultInterface', $operatingSystemResult);

        $this->assertEquals('Mac OSX', $operatingSystemResult->getFamily());
        $this->assertInternalType('string', $operatingSystemResult->getFamily());

        $this->assertEquals(10, $operatingSystemResult->getMajor());
        $this->assertInternalType('string', $operatingSystemResult->getMajor());

        $this->assertEquals(8, $operatingSystemResult->getMinor());
        $this->assertInternalType('string', $operatingSystemResult->getMinor());

        $this->assertEquals(3, $operatingSystemResult->getPatch());
        $this->assertInternalType('string', $operatingSystemResult->getPatch());

        $this->assertEquals('Mac OSX 10.8.3', $operatingSystemResult->__toString());
    }
}