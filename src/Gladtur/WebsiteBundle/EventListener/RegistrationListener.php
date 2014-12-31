<?php
/**
 * Created by PhpStorm.
 * User: mortenthorpe
 * Date: 02/07/14
 * Time: 21.19
 */

namespace Gladtur\WebsiteBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use FOS\UserBundle\Event\UserEvent;
use Symfony\Component\HttpFoundation\Session\Session;
/**
 * Listener responsible for adding the default user role at registration
 */
class RegistrationListener implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
        );
    }

    public function onRegistrationSuccess(FormEvent $event)
    {
        $rolesArr = array('ROLE_USER', 'ROLE_TVGUSER');

        /** @var $user \FOS\UserBundle\Model\UserInterface */
        $user = $event->getForm()->getData();
        $user->setRoles($rolesArr);
    }
}