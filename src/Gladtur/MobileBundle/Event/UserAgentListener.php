<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 7/1/13
 * Time: 11:25 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Gladtur\MobileBundle\Event;

use Gladtur\MobileBundle\Controller\UserAgentProceedController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class UserAgentListener
{
    private $tokens;

    public function __construct($useragents)
    {
        $this->useragents = $useragents;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /*
         * $controller passed can be either a class or a Closure. This is not usual in Symfony2 but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof UserAgentProceedController) {
            $useragents = $event->getRequest()->query->get('useragents');
            if (!in_array($useragents, $this->useragents)) {
                throw new AccessDeniedHttpException('This action needs a valid user agent!');
            }
        }
    }
}