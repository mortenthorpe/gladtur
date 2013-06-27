<?php

/*
 * This file is part of the FOSOAuthServerBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gladtur\MobileBundle\Controller;

use FOS\OAuthServerBundle\Event\OAuthEvent;
use FOS\OAuthServerBundle\Form\Handler\AuthorizeFormHandler;
use OAuth2\OAuth2;
use OAuth2\OAuth2ServerException;
use OAuth2\OAuth2RedirectException;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller handling basic authorization
 *
 * @author Chris Jones <leeked@gmail.com>
 */
class AuthorizeController extends ContainerAware
{
    /**
     * @var \FOS\OAuthServerBundle\Model\ClientInterface
     */
    private $client;

    /**
     * Authorize
     * @Route("/auth")
     */
    public function authorizeAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (!$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        if (true === $this->container->get('session')->get('_fos_oauth_server.ensure_logout')) {
            $this->container->get('session')->invalidate(600);
            $this->container->get('session')->set('_fos_oauth_server.ensure_logout', true);
        }

        $scope = $this->container->get('request')->get('scope', null);

        return $this->container
            ->get('fos_oauth_server.server')
            ->finishClientAuthorization(true, $user, $request, $scope);

        return new Response('the token!');

        return $this->redirect('admin/tag');

    }
}
