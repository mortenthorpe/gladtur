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
     * @Route("register_deprecated")
     */
    public function registerAction(Request $request)
    {
        /**
         * @var UserManager $userManager
         */
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserByUsernameOrEmail($request->get('username'));
        if (!$user) {
            /**
             * @var User $user
             */
            $user = $userManager->createUser();
            $user->setEnabled(true);
            $user->setUsername($request->get('username'));
            $user->setEmail($request->get('email'));
            $user->setRoles(array('ROLE_TVGUSER'));
            $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
            $encodedPass = $encoder->encodePassword($request->get('password'), $user->getSalt());
            $user->setPassword($encodedPass);
            $userManager->updateUser($user);

            return parent::getJsonForData(array('success' => 1));
        } else {
            return parent::getJsonForData(array('success' => 0));
        }
    }

    /**
* @param Request $request
 * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("register")
     */
    public function registerJSONAction(Request $request){
        /**
         * @var UserManager $userManager
         */

        if(parent::getIsJSON()){
            $reqAssoc = parent::getRequestFromJSON($request);
            $username = $reqAssoc['username'];
            $email = $reqAssoc['email'];
            $password = $reqAssoc['password'];
        }
        else{
            $username = $request->get('username');
            $email = $request->get('email');
            $password = $request->get('password');
        }
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
            return parent::getJsonForData(array('success' => 1, 'token' => $user->getSalt()));
        } else {
            return parent::getJsonForData(array('success' => 0));
        }
    }
    /**
     * @Route("forgotpassword/{usernameoremail}/{newpassword}/sendmail/{atomic}", defaults={"atomic" = 1})
     * @Method({"GET", "POST"})
     */
    public function forgotPasswordAction($usernameoremail, $newpassword, $atomic)
    {
        $atomic = (bool) $atomic;
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
        if(($response == 1) && $atomic){
            // Send the email automatically!
            return $this->sendForgotpasswordEmail($usernameoremail);
        }
        else{
            return parent::getJsonForData(array('success' => $response));
        }
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
                ->setFrom('account@edge.gladtur.morning.dk')
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

    /**
     * @Route("deleteuser")
     */
    public function deleteUserAction(){
        $success = 0; // User does not exist, or is soft-deleted! //
        $username = $this->getRequest()->get('username', null);
        $userId = $this->getRequest()->get('id', null);
        $userManager = $this->container->get('fos_user.user_manager');
        if($username){
            $user = $userManager->findUserByUsernameOrEmail($username);
        }
        if($userId){
            $user = $userManager->findUserBy(array('id'=>$userId));
        }

        /**
         * @var User $user
         */
        if($user){
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
            $success = 1;
        }
        return parent::getJsonForData(array('success' => $success));
    }

    /**
     * @Route("undeleteuser")
     */
    public function undeleteUserAction(){
        $em = $this->getDoctrine()->getManager();
        $filters = $em->getFilters();
        $filters->disable('softdeleteable');
        $um = $this->container->get('fos_user.user_manager');
        if($this->getRequest()->get('username', null)){
            $user = $um->findUserByUsernameOrEmail($this->getRequest()->get('username'));
        }
        elseif($this->getRequest()->get('id', null)){
            $user = $um->findUserBy(array('id'=>$this->getRequest()->get('id')));
        }
        $user->setDeletedAt(false);
        $em->persist($user);
        $em->flush();
        return parent::getJsonForData(array('success' => 1));
    }

    /**
     * @param Request $request
     * @Route("mobile/login")
     */
    public function loginAction(Request $request){
        if(parent::getIsJSON()){
        $requestContent = parent::getRequestFromJSON($request);
        if(!isset($requestContent['username']) || !isset($requestContent['password'])) return parent::getJsonForData(array('success' => 0));
        $um = $this->container->get('fos_user.user_manager');
        $user = $um->findUserBy(array('username'=>$requestContent['username']));
        if($user){
            $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
            $encodedUserRefPass = $user->getPassword();
            $encodedReqPass = $encoder->encodePassword($requestContent['password'], $user->getSalt());
            if($encodedReqPass == $encodedUserRefPass) return parent::getJsonForData(array('success' => 1, 'token'=>$user->getSalt()));
        }
        }
        return parent::getJsonForData(array('success' => 0));
    }
}