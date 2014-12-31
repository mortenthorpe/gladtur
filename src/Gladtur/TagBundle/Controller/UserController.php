<?php
/**
 * Created by PhpStorm.
 * User: mortenthorpe
 * Date: 06/01/14
 * Time: 10:41
 */

namespace Gladtur\TagBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Gladtur\WebsiteBundle\Form\Type\ProfileFormType;
//use Gladtur\TagBundle\Entity\User as GladUser;

class UserController extends Controller{

    /**
     * @Route("/users/list", name="admin_users_list")
     * @Template("GladturTagBundle:Users:list.html.twig")
     * @param Request $request
     * @return array
     */
    public function listUsersAction(Request $request){
      $userManager = $this->container->get('fos_user.user_manager');
      return array(
          'users' => $userManager->findUsers()
      );
    }

    /**
     * @Route("/user/show", name="admin_user_show")
     * @Template("GladturTagBundle:Users:showuser.html.twig")
     */
    public function userShowAction(Request $request){
        $user = $this->container->get('fos_user.user_manager')->findUserByUsername($request->get('username'));
        return array(
           'user' => $user
        );
    }

    /**
     * @Route("user/enable", name="admin_user_enable")
     */
    public function userEnableAction(Request $request){
        $user = $this->container->get('fos_user.user_manager')->findUserByUsername($request->get('username'));
        $user->setEnabled(true);
        $this->container->get('fos_user.user_manager')->updateUser($user);
    }

    /**
     * @Route("user/disable", name="admin_user_disable")
     */
    public function userDisableAction(Request $request){
        $user = $this->container->get('fos_user.user_manager')->findUserByUsername($request->get('username'));
        $user->setEnabled(false);
        $this->container->get('fos_user.user_manager')->updateUser($user);
    }

    /**
     * @Route("user/edit", name="admin_user_edit")
     * @Template("GladturTagBundle:Users:edituser.html.twig")
     */
    public function userEditAction(Request $request){
        $user = $this->container->get('fos_user.user_manager')->findUserByUsername($request->get('username'));
        $form = $this->createForm(new ProfileFormType('Gladtur\TagBundle\Entity\User'), $user);
        return array(
            'userid' => $user->getId(),
            'form' => $form->createView()
        );
    }

    /**
     * @Route("user/savechanges", name="admin_user_savechanges")
     */
    public function userSavechangesAction(Request $request){
        $reqArray = $request->get('gladtur_user_profile');
        $user = $this->container->get('fos_user.user_manager')->findUserByUsername($reqArray['username']);
        $form = $this->createForm(new ProfileFormType('Gladtur\TagBundle\Entity\User'), $user);
        $form->bind($request);
        $this->container->get('fos_user.user_manager')->updateUser($user);
        return $this->redirect($this->generateUrl('admin_user_edit', array('username' => $reqArray['username'])));
    }
} 