<?php

namespace UAParser\Tests\Result;

use UAParser\Result\RenderingEngineResult;

/**
 * @author Benjamin Laugueux <benjamin@yzalis.com>
 */
class RenderingEngineResultTest extends \PHPUnit_Framework_TestCase
{
    public function testFromArray()
    {
        $result = new RenderingEngineResult();
        $result->fromArray(array(
            'family'   => 'Trident',
            'version'  => '4.0',
        ));

        $this->assertInstanceOf('UAParser\Result\RenderingEngineResult', $result);
        $this->assertInstanceOf('UAParser\Result\RenderingEngineResultInterface', $result);

        $this->assertEquals('Trident', $result->getFamily());
        $this->assertInternalType('string', $result->getFamily());

        $this->assertEquals('4.0', $result->getVersion());
        $this->assertInternalType('string', $result->getVersion());

        $this->assertEquals('Trident 4.0', $result->__toString());
    }
}