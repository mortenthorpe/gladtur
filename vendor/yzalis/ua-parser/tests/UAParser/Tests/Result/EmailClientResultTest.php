<?php

namespace UAParser\Tests\Result;

use UAParser\Result\EmailClientResult;

/**
 * @author Benjamin Laugueux <benjamin@yzalis.com>
 */
class EmailClientResultTest extends \PHPUnit_Framework_TestCase
{
    public function testFromArray()
    {
        $emailClientResult = new EmailClientResult();
        $emailClientResult->fromArray(array(
            'family' => 'Thunderbird',
            'major'  => '3',
            'minor'  => '1',
            'patch'  => '2',
            'type'  => 'desktop',
        ));

        $this->assertInstanceOf('UAParser\Result\EmailClientResult', $emailClientResult);
        $this->assertInstanceOf('UAParser\Result\EmailClientResultInterface', $emailClientResult);

        $this->assertEquals('Thunderbird', $emailClientResult->getFamily());
        $this->assertInternalType('string', $emailClientResult->getFamily());

        $this->assertEquals('3', $emailClientResult->getMajor());
        $this->assertInternalType('string', $emailClientResult->getMajor());

        $this->assertEquals('1', $emailClientResult->getMinor());
        $this->assertInternalType('string', $emailClientResult->getMinor());

        $this->assertEquals('2', $emailClientResult->getPatch());
        $this->assertInternalType('string', $emailClientResult->getPatch());

        $this->assertEquals('desktop', $emailClientResult->getType());
        $this->assertInternalType('string', $emailClientResult->getType());

        $this->assertEquals('Thunderbird 3.1.2', $emailClientResult->__toString());
        $this->assertInternalType('string', $emailClientResult->__toString());
    }
}