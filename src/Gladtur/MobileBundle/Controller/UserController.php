<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 6/11/13
 * Time: 10:46 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Gladtur\MobileBundle\Controller;

use Doctrine\ORM\Query;
use FOS\RestBundle\View\View;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\FOSUserEvents;

//use FOS\UserBundle\Model\User;
use Gladtur\TagBundle\Entity\User;

//use FOS\UserBundle\Model\UserManager;
use Gladtur\TagBundle\Controller\JsonController;
use Gladtur\TagBundle\Entity\UserPassword;
use Swift_Message;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

//use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Doctrine\UserManager;


class UserController extends JsonController implements ContainerAwareInterface
{
    /**
     * @Route("/userexists/username/{usernameoremail}")
     */
    public function userexistsAction($usernameoremail)
    {
        $response = 1;
        $userManager = $this->container->get(
            'fos_user.user_manager'
        ); // SRC: http://stackoverflow.com/questions/12656388/symfony2-how-to-get-user-object-inside-controller-when-using-fosuserbundle //
        $user = $userManager->findUserByUsernameOrEmail($usernameoremail);
        if (!$user) {
            $response = 0;
        }

        return parent::getJsonForData(array('success' => $response));
    }

    /**
     * @param $usernameoremail
     * @return mixed
     * @Route("rest/userexists/username/{usernameoremail}")
     */
    public function userfinderAction()
    {
        $data = $this->getDoctrine()->getManager()
            ->createQuery('SELECT u FROM Gladtur\TagBundle\Entity\User u')
            ->setFetchMode(
                'Gladtur\TagBundle\Entity\User',
                'user_location_data',
                \Doctrine\ORM\Mapping\ClassMetadata::FETCH_EXTRA_LAZY
            )
            ->setMaxResults(1)
            ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)
            ->getResult();
        $view = new View($data);
        $view->setFormat('json');

        //$view->setTemplate('LiipHelloBundle:Rest:getArticles.html.twig');
        return $this->get('fos_rest.view_handler')->handle($view);
    } // "rest_user_exists" [GET] rest/userexists/username/{usernameoremail}
    /**
     * @Route("usercreate/username/{username}/email/{email}/password/{password}")
     */
    public function registerAction($username, $email, $password)
    {
        /**
         * @var UserManager $userManager
         */
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserByUsernameOrEmail($username);
        if (!$user) {
            /**
             * @var User $user
             */
            $user = $userManager->createUser();
            $user->setEnabled(true);
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setRoles(array('ROLE_TVGUSER'));
            $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
            $encodedPass = $encoder->encodePassword($password, $user->getSalt());
            $user->setPassword($encodedPass);
            $userManager->updateUser($user);

            return parent::getJsonForData(array('success' => 1));
        } else {
            return parent::getJsonForData(array('success' => 0));
        }
    }

    /**
     * @Route("forgotpassword/{usernameoremail}/{newpassword}")
     * @Method({"GET", "POST"})
     */
    public function forgotPasswordAction($usernameoremail, $newpassword)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        /**
         * @var User $user
         */
        $user = $userManager->findUserByUsernameOrEmail($usernameoremail);
        $response = 1;
        if ($user) {
            $em = $this->getDoctrine()->getManager();
            $userTmpExisting = $em->getRepository('Gladtur\TagBundle\Entity\UserPassword')->findOneBy(
                array('user' => $user->getId())
            );
            if ($userTmpExisting) {
                /*$em->detach($user); // This releases the user from being affected by the removal of the attached UserPassword entity //
                $em->remove($userTmpExisting);
                $em->flush();*/
                $this->getDoctrine()->getManager()
                    ->createQuery(
                        'DELETE FROM Gladtur\TagBundle\Entity\UserPassword up where up.id = ' . $userTmpExisting->getId(
                        )
                    )
                    ->setFetchMode(
                        'Gladtur\TagBundle\Entity\UserPassword',
                        'user',
                        \Doctrine\ORM\Mapping\ClassMetadata::FETCH_EXTRA_LAZY
                    )
                    ->getResult();
            }
            $userTmpPassword = new UserPassword();
            $userTmpPassword->setUser($user);
            $userTmpPassword->setUsernameoremail($usernameoremail);
            $userTmpPassword->setNewpasswordPlain($newpassword);
            $em = $this->getDoctrine()->getManager();
            $em->persist($userTmpPassword);
            $em->flush();
        } else {
            $response = 0;
        }

        return parent::getJsonForData(array('success' => $response));
    }

    /**
     * @Route("sendforgottenmail/{usernameOrEmail}")
     * @Method({"GET", "POST"})
     */
    public function sendForgotpasswordEmail($usernameOrEmail)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        /**
         * @var User $user
         */
        $response = 0;
        $user = $userManager->findUserByUsernameOrEmail($usernameOrEmail);
        if ($user) {
            $message = Swift_Message::newInstance()
                ->setSubject('Gladtur - Nyt kodeord til dig!')
                ->setFrom('morten@edge.gladtur.morning.dk')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'GladturMobileBundle:Default:email_forgottenpassword.txt.twig',
                        array(
                            'username' => $user->getUsername(),
                            'rellink' => '/setpassword/' . $usernameOrEmail . '/',
                            'secret' => 'blablabla'
                        )
                    )
                );
            $response = @$this->get('mailer')->send($message);
            if ($response) $response = 1;
        }

        return parent::getJsonForData(array('success' => $response));
    }

    /**
     * @Route("setpassword/{usernameoremail}/{secretcode}")
     * @Method({"GET", "POST"})
     */
    public function setPasswordAction($usernameoremail, $secretcode)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        /**
         * @var User $user
         */
        $response = 0;
        $em = $this->getDoctrine()->getManager();
        /**
         * @var UserPassword $userTmpExisting
         */
        $userTmpExisting = $em->getRepository('Gladtur\TagBundle\Entity\UserPassword')->findOneBy(
            array('usernameoremail' => $usernameoremail)
        );
        $user = $userManager->findUserByUsernameOrEmail($usernameoremail);
        if ($user->getNewpassword()) {
            $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
            $encodedPass = $encoder->encodePassword($userTmpExisting->getNewpasswordPlain(), $user->getSalt());
            $user->setPassword($encodedPass);
            $userManager->updateUser($user);
            $this->getDoctrine()->getManager()
                ->createQuery(
                    'DELETE FROM Gladtur\TagBundle\Entity\UserPassword up where up.id = ' . $userTmpExisting->getId()
                )
                ->setFetchMode(
                    'Gladtur\TagBundle\Entity\UserPassword',
                    'user',
                    \Doctrine\ORM\Mapping\ClassMetadata::FETCH_EXTRA_LAZY
                )
                ->getResult();
            $response = 1;
            $em->flush();
        }

        return parent::getJsonForData(array('success' => $response));
    }
}