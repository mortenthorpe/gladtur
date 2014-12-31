<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 7/25/13
 * Time: 12:03 PM
 * To change this template use File | Settings | File Templates.
 */
require __DIR__.'/vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespace('Gladtur\TagBundle\Model', __DIR__.'/../src/Gladtur/TagBundle/Model');
$loader->register();