<?php
/**
 * Created by PhpStorm.
 * User: mortenthorpe
 * Date: 26/11/13
 * Time: 10:27
 */

namespace Gladtur\TagBundle\EventListener;


use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class PasswordResettingListener implements EventSubscriberInterface
{
    private $router;
    private $container;

    public function __construct(UrlGeneratorInterface $router, $container)
    {
        $this->container = $container;
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::RESETTING_RESET_SUCCESS => 'onPasswordResettingSuccess',
        );
    }

    public function onPasswordResettingSuccess(FormEvent $event)
    {
        $url = $this->router->generate('homepage');
        $this->container->get('session')->getFlashBag()->add('notice', 'Din adgangskode er nu blevet skiftet');
        $event->setResponse(new RedirectResponse($url));
    }
}