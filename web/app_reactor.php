<?php
// #web/app_reactor.php

require_once __DIR__ . '/../app/bootstrap.php.cache';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

define('KERNEL_ROOT', __DIR__ . '/../app'); //this fixes a kernel root error you might experience

$app = new Blackshawk\SymfonyReactorBundle\Reactor\Kernel('dev', true); //run in dev mode with debug mode on
$stack = new React\Espresso\Stack($app);
$stack->listen(1337);