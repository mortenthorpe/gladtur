# UAParser [![Build Status](https://secure.travis-ci.org/yzalis/UAParser.png)](http://travis-ci.org/yzalis/UAParser)

**UAParser** is a library which helps you to parse user agents and detect browser, operating system, device and more.

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/13b4b7d1-6c03-418b-8ac2-b5accbf3b67a/small.png)](https://insight.sensiolabs.com/projects/13b4b7d1-6c03-418b-8ac2-b5accbf3b67a)

## Basic Usage
```php
<?php

// create a new UAParser instance
$uaParser = new \UAParser\UAParser();

// ...or optionally load a custom regexes.yml file of your choice
// $uaParser = new \UAParser\UAParser(__DIR__.'/../../custom_regexes.yml');

// parse a user agent string an get the result
$result =  $uaParser->parse('Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20130406 Firefox/23.0.1');
```

## Results

### Global Result API
* `$result->getBrowser()` will return a `UAParser\Result\BrowserResult` object
* `$result->getOperatingSystem()` will return a `UAParser\Result\OperatingSystemResult` object
* `$result->getDevice()` will return a `UAParser\Result\DeviceResult` object
* `$result->getRenderingEngine()` will return a `UAParser\Result\RenderingEngineResult` object
* `$result->getEmailClient()` will return a `UAParser\Result\EmailClientResult` object

### Browser
* `$result->getBrowser()->getFamily()` will return a string like `Firefox`
* `$result->getBrowser()->getMajor()` will return an integer like `23`
* `$result->getBrowser()->getMinor()` will return an integer like `0`
* `$result->getBrowser()->getPatch()` will return an integer like `1`
* `$result->getBrowser()->getVersionString()` will return a string like `23.0.1`

### Operating System
* `$result->getOperatingSystem()->getFamily()` will return a string like `Mac OS`
* `$result->getOperatingSystem()->getMajor()` will return a string like `10`
* `$result->getOperatingSystem()->getMinor()` will return a string like `8`
* `$result->getOperatingSystem()->getPatch()` will return a string like `4`

### Device
* `$result->getDevice()->getConstructor()` will return a string like `Apple`
* `$result->getDevice()->getModel()` will return a string like `iPhone`
* `$result->getDevice()->getType()` will return a string like `mobile`
* `$result->getDevice()->isMobile()` will return a boolean like `true`
* `$result->getDevice()->isTablet()` will return a boolean like `false`
* `$result->getDevice()->isDesktop()` will return a boolean like `false`
* `$result->getDevice()->is('mobile')` will return a boolean like `false`
* `$result->getDevice()->is('tablet')` will return a boolean like `false`
* `$result->getDevice()->is('desktop')` will return a boolean like `false`

### EmailClient
* `$result->getEmailClient()->getFamily()` will return a string like `Thunderbird`
* `$result->getEmailClient()->getMajor()` will return a string like `3`
* `$result->getEmailClient()->getMinor()` will return a string like `1`
* `$result->getEmailClient()->getPatch()` will return a string like `2`
* `$result->getEmailClient()->getType()` will return a string like `desktop`
* `$result->getEmailClient()->isDesktop()` will return a boolean like `true`
* `$result->getEmailClient()->isWebmail()` will return a boolean like `false`
* `$result->getEmailClient()->is('desktop')` will return a boolean like `true`
* `$result->getEmailClient()->is('webmail')` will return a boolean like `false`

### Rendering Engine
* `$result->getRenderingEngine()->getFamily()` will return a string like `Trident`
* `$result->getRenderingEngine()->getVersion()` will return a string like `4.0`

## Unit Tests

To run unit tests, you'll need cURL and a set of dependencies you can install using Composer:
```
curl -sS https://getcomposer.org/installer | php
php composer.phar install
```

Once installed, just launch the following command:
```
./vendor/bin/phpunit
```

You're done.

## Credits

* Benjamin Laugueux <benjamin@yzalis.com>
* [All contributors](https://github.com/yzalis/UAParser/contributors)

Thanks for providing a huge amount of data to run tests:
* [http://user-agent-string.info](http://user-agent-string.info)
* [http://www.useragentstring.com](http://www.useragentstring.com)

## License

UAParser is released under the MIT License. See the bundled LICENSE file for details.
