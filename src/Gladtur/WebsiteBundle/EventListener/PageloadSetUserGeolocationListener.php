<?php
/**
 * Created by PhpStorm.
 * User: mortenthorpe
 * Date: 27/11/13
 * Time: 11:56
 */

namespace Gladtur\WebsiteBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class PageloadSetUserGeolocationListener implements EventSubscriberInterface{

   /* private $pagesWithTitles;

    public function __construct($pagesWithTitles){
        $this->pagesWithTitles = $pagesWithTitles;
    }*/

    public static function getSubscribedEvents()
    {
        return array(
            // must be registered before the default Locale listener
            KernelEvents::RESPONSE => array('onKernelResponse'),
        );
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
    /*    $req_route = $event->getRequest()->attributes->get('_route');
        if (in_array($req_route, $this->pagesWithTitles)) {
            $pagetitle = $this->pagesWithTitles[$req_route];
        }
        else{
            $pagetitle = '';
        }*/
        if(!$event->getRequest()->getSession()->get('ulat',null) || !$event->getRequest()->getSession()->get('ulng',null) ){
        $user = $event->getRequest()->getUser();
        if ($user instanceof UserInterface) {
            $event->getRequest()->getSession()->set('positionKnown', true);
            $event->getRequest()->getSession()->set('ulat',$user->getLatitude());
            $event->getRequest()->getSession()->set('ulng',$user->getLongitude());
        }
        else{
            $event->getRequest()->getSession()->set('positionKnown', true);
            // Rådhuspladsen GetCoords: 55.675283,12.570163
            $event->getRequest()->getSession()->set('ulat',55.675283);
            $event->getRequest()->getSession()->set('ulng',12.570163);
        }
        }
    }
} 