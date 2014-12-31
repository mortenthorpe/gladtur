<?php
/**
 * Created by PhpStorm.
 * User: mortenthorpe
 * Date: 13/08/14
 * Time: 23.46
 */

namespace Gladtur\WebsiteBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Http\SecurityEvents;

class FOSPostLoginListener implements EventSubscriberInterface{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return array(
            //SecurityEvents::SWITCH_USER => 'onSecurityInteractiveSwitchUser',
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
        );
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        if ($user instanceof UserInterface) {
            if($user->hasLatitude() && $user->hasLongitude()){
                $this->container->get('session')->set('positionKnown', true);
                $this->container->get('session')->set('ulat',$user->getLatitude());
                $this->container->get('session')->set('ulng',$user->getLongitude());
            }
            else{
                $this->container->get('session')->set('positionKnown', false);
                // Rådhuspladsen GetCoords: 55.675283,12.570163
                $this->container->get('session')->set('ulat',55.675283);
                $this->container->get('session')->set('ulng',12.570163);
            }
        }
    }

    public function onSecurityInteractiveSwitchUser(InteractiveLoginEvent $event){
        $user = $event->getAuthenticationToken()->getUser();
        if ($user instanceof UserInterface) {
            if($user->hasLatitude() && $user->hasLongitude()){
                $this->container->get('session')->set('positionKnown', true);
            }
            else{
              $this->container->get('session')->set('positionKnown', false);
            }
        }
        else{
            $this->container->get('session')->set('positionKnown', true);
        }
    }
}