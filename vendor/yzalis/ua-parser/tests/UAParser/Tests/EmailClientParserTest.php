<?php

namespace UAParser\Tests;

use UAParser\UAParser;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Benjamin Laugueux <benjamin@yzalis.com>
 */
class EmailClientParserTest extends \PHPUnit_Framework_TestCase
{
    protected $uaParser;

    public function setUp()
    {
        $this->uaParser = new UAParser();
    }
        
    /**
     * @dataProvider emailClientDataProvider
     */
    public function testEmailClientParser($uaString, $expectedFamily, $expectedMajor, $expectedMinor, $expectedPatch, $expectedType = 'desktop')
    {
        $result = $this->uaParser->parse($uaString);
        
        $this->assertEquals($expectedFamily, $result->getEmailClient()->getFamily());
        $this->assertEquals($expectedMajor, $result->getEmailClient()->getMajor());
        $this->assertEquals($expectedMinor, $result->getEmailClient()->getMinor());
        $this->assertEquals($expectedPatch, $result->getEmailClient()->getPatch());
        $this->assertEquals($expectedType, $result->getEmailClient()->getType());
    }

    public function emailClientDataProvider()
    {
        return Yaml::parse(__DIR__.'/Fixtures/email_clients.yml');
    }
        
    /**
     * @dataProvider emailClientRefererDataProvider
     */
    public function testEmailClientRefererParser($referer, $expectedFamily, $expectedType = 'desktop')
    {
        $result = $this->uaParser->parse($referer);

        $this->assertEquals($expectedFamily, $result->getEmailClient()->getFamily());
        $this->assertEquals($expectedType, $result->getEmailClient()->getType());
    }

    public function emailClientRefererDataProvider()
    {
        return Yaml::parse(__DIR__.'/Fixtures/referers.yml');
    }
}