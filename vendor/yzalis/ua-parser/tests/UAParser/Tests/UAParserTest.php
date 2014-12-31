<?php

namespace UAParser\Tests;

use UAParser\UAParser;

/**
 * @author Benjamin Laugueux <benjamin@yzalis.com>
 */
class UAParserTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorLoadsDefaultRegexesPath()
    {
        $uaParser = new UAParser();

        $result = $uaParser->parse('Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.17 Safari/537.36');

        $this->assertSame($result->getBrowser()->getFamily(), 'Chrome');
    }

    public function testConstructorLoadsCustomRegexesPath()
    {
        $uaParser = new UAParser(__DIR__.'/Fixtures/custom_regexes.yml');

        $result = $uaParser->parse('Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.17 Safari/537.36');

        $this->assertSame($result->getBrowser()->getFamily(), 'Chrome Custom Regex Path Test');
    }

    public function testCustomRegexesFileWithMissingCategoryKey()
    {
        $uaParser = new UAParser(__DIR__.'/Fixtures/empty_regexes.yml');

        $result = $uaParser->parse('');

        $this->assertSame('Other', $result->getBrowser()->getFamily());
        $this->assertSame('Other', $result->getOperatingSystem()->getFamily());
        $this->assertSame('Other', $result->getDevice()->getConstructor());
        $this->assertSame('Other', $result->getEmailClient()->getFamily());
        $this->assertSame('Other', $result->getRenderingEngine()->getFamily());
    }

    public function testClass()
    {
        $uaParser = new UAParser();
        $result = $uaParser->parse('');

        $this->assertInstanceOf('UAParser\UAParser', $uaParser);
        $this->assertInstanceOf('UAParser\UAParserInterface', $uaParser);
        $this->assertInstanceOf('UAParser\Result\DeviceResult', $result->getDevice());
        $this->assertInstanceOf('UAParser\Result\DeviceResultInterface', $result->getDevice());
        $this->assertInstanceOf('UAParser\Result\OperatingSystemResult', $result->getOperatingSystem());
        $this->assertInstanceOf('UAParser\Result\OperatingSystemResultInterface', $result->getOperatingSystem());
        $this->assertInstanceOf('UAParser\Result\BrowserResult', $result->getBrowser());
        $this->assertInstanceOf('UAParser\Result\BrowserResultInterface', $result->getBrowser());
        $this->assertInstanceOf('UAParser\Result\EmailClientResult', $result->getEmailClient());
        $this->assertInstanceOf('UAParser\Result\EmailClientResultInterface', $result->getEmailClient());
        $this->assertInstanceOf('UAParser\Result\RenderingEngineResult', $result->getRenderingEngine());
        $this->assertInstanceOf('UAParser\Result\RenderingEngineResultInterface', $result->getRenderingEngine());
    }
}
