<?php

namespace UAParser\Tests;

use UAParser\UAParser;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Benjamin Laugueux <benjamin@yzalis.com>
 */
class RenderingEngineParserTest extends \PHPUnit_Framework_TestCase
{
    protected $uaParser;

    public function setUp()
    {
        $this->uaParser = new UAParser();
    }
        
    /**
     * @dataProvider renderingEngineDataProvider
     */
    public function testRenderingEngineParser($uaString, $expectedFamily, $expectedVersion)
    {
        $result = $this->uaParser->parse($uaString);

        $this->assertEquals($expectedFamily, $result->getRenderingEngine()->getFamily());
        $this->assertEquals($expectedVersion, $result->getRenderingEngine()->getVersion());
    }

    public function renderingEngineDataProvider()
    {
        return Yaml::parse(__DIR__.'/Fixtures/rendering_engines.yml');
    }
}