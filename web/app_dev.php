<?php
umask(0000);
use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';

// Use APC for autoloading to improve performance
// Change 'sf2' by the prefix you want in order to prevent key conflict with another application

$loader = new ApcClassLoader('sf2', $loader);
$loader->register(true);


require_once __DIR__.'/../app/AppKernel.php';
require_once __DIR__.'/../app/AppCache.php';// Comment for uncached!

$kernel = new AppKernel('dev', true);
$kernel->loadClassCache(); // Comment for uncached!
$kernel = new AppCache($kernel); // Comment for uncached!
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
