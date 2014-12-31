<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Ivory\GoogleMap;

$loader = require __DIR__.'/../vendor/autoload.php';

// intl
if (!function_exists('intl_get_error_code')) {
    require_once __DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs/functions.php';

    $loader->add('', __DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs');
}

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

$loader->add('FOS', __DIR__.'/../vendor/bundles');
$loader->add('Buzz', __DIR__.'/../vendor/bundles/Buzz/lib');
$loader->add('Google', __DIR__.'/../vendor');
$loader->add('FOS\\Rest', __DIR__.'/../vendor/fos');
/*$loader->add('Knp\\Menu',realpath(__DIR__ . '/../vendor/knplabs/knp-menu/src'));
$loader->add('Knp\\Bundle\\MenuBundle',realpath(__DIR__ . '/../vendor/knplabs/knp-menu-bundle'));*/
$loader->add('Liip\\ImagineBundle\\LiipImagineBundle', realpath(__DIR__ . '/../vendor/liip/imagine-bundle/Liip/ImagineBundle'));
$loader->add('Imagine', realpath(__DIR__.'/../vendor/imagine/imagine/lib/'));
//$loader->add('CCDNUser\\SecurityBundle', realpath(__DIR__.'/../vendor/codeconsortium/ccdn-user-security-bundle/CCDNUser'));
//$loader->add('Blackshawk', realpath(__DIR__.'/../vendor/bundles'));
$classLoader = new \Doctrine\Common\ClassLoader('DoctrineExtensions', realpath(__DIR__.'/../vendor/beberlei/DoctrineExtensions/lib'));
$classLoader->register();
return $loader;
