<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 6/18/13
 * Time: 12:06 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Gladtur\MobileBundle\Event;

use FOS\OAuthServerBundle\Event\OAuthEvent;
use OAuth2\OAuth2;

/*use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\Criteria;*/

// #Source - FOS oAuthBundle Example - https://github.com/FriendsOfSymfony/FOSOAuthServerBundle/blob/master/Resources/doc/the_oauth_event_class.md
class oAuthPreAuthListener
{
    public function onPreAuthorizationProcess(OAuthEvent $event)
    {
        $user = $event->getUser();
        if ($event->isAuthorizedClient()) {
            $oAuthServerInstance = new OAuth2();
            $oAuthServerInstance->finishClientAuthorization(true, $user, null, $scope);
        }
    }

    protected function getUser(OAuthEvent $event)
    {
        return $event->getUser();
    }
}