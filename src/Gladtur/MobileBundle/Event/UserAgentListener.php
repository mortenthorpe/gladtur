<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 7/1/13
 * Time: 11:25 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Gladtur\MobileBundle\Event;

use Gladtur\MobileBundle\Controller\UserAgentJSONController;
use Gladtur\MobileBundle\Controller\UserAgentProceedController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class UserAgentListener
{
    private $useragents;

    public function __construct($useragents)
    {
        $this->useragents = $useragents;
    }

    public function onKernelControllerPermission(FilterControllerEvent $event)
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
            //$useragents = $event->getRequest()->query->get('useragents');
            $useragents = $event->getRequest()->headers->get('user-agent');
           if (!in_array($useragents, array_values($this->useragents))) {
                throw new AccessDeniedHttpException('This action needs a valid user agent! '.json_encode(array_values($this->useragents)));
            }
        }
    }

    public function onKernelControllerReceiveJSON(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof UserAgentJSONController) {
            if($event->getRequest()->headers->get('Content-Type') == 'application/json'){
                $controller[0]->setConvertFromJSON(true);
            };
        }
    }
}