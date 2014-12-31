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
use Gladtur\TagBundle\Entity\UserProfileByTags;
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
     * @Route("userexists/username/{usernameoremail}")
     * @Method("GET")
     */
    public function userexistsAction($usernameoremail)
    {
        $response = 1;
        $userManager = $this->container->get(
            'fos_user.user_manager'
        ); // SRC: http://stackoverflow.com/questions/12656388/symfony2-how-to-get-user-object-inside-controller-when-using-fosuserbundle //
        $user = $userManager->findUserByUsernameOrEmail($usernameoremail);
        if (!$user || !$user->isEnabled()) {
            $response = 0;
        }

        return parent::getJsonForData(array('success' => $response));
    }

    /**
     * @param $usernameoremail
     * @return mixed
     * @Route("rest/userexists/username/{usernameoremail}")
     * @Method("GET")
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
     * @Method("POST")
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
     * @Route("register")
     * @Method("POST")
     */
    public function registerJSONAction(Request $request){
        $content = $request->getContent();
        $actionName = $request->attributes->get('_controller');
        $actionName = str_replace(array('::', '\\'), array('-', '-'), $actionName);
        if(parent::getIsJSON()){
                $reqAssoc = $request->getContent();
                $actionName = $request->attributes->get('_controller');
                $actionName = str_replace(array('::', '\\'), array('-', '-'), $actionName);
                //file_put_contents('/symftemp/'.$actionName.'__at_'.date('d-m-h_i_s').'__raw.txt', $reqAssoc);
            $reqAssoc = substr($reqAssoc,1);
            $reqAssoc = substr($reqAssoc,0,-1);
            $reqAssoc = '{'.$reqAssoc.'}';
            $reqAssoc = json_decode($reqAssoc, true);
            $username = $reqAssoc['username'];
            $email = $reqAssoc['email'];
            $password = $reqAssoc['password'];
            $newsletter = (isset($reqAssoc['newsletter']) && ($reqAssoc['newsletter']==1))?true:false;
            $contact = (isset($reqAssoc['contactme']) && ($reqAssoc['contactme']==1))?true:false;
            $profileId = (isset($reqAssoc['profileid']) && ($reqAssoc['profileid']!==''))?intval($reqAssoc['profileid']):3;
        }
        else{
            $username = $request->get('username');
            $email = $request->get('email');
            $password = $request->get('password');
            $newsletter = $request->get('newsletter', null);
            $contact = $request->get('contactme', null);
            $profileId = $request->get('profileid', 3);
        }
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);
        if(!$user){
            $user = $userManager->findUserByEmail($email);
        }
        if (!$user) {
            /**
             * @var User $user
             */
            $user = $userManager->createUser();
            $user->setEnabled(true);
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setNewsletter($newsletter);
            $user->setContact($contact);
            $user->setRoles(array('ROLE_TVGUSER'));
            $userProfile = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\TvguserProfile', $profileId);
            if($userProfile){
                $user->setProfile($userProfile);
            }
            $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
            $encodedPass = $encoder->encodePassword($password, $user->getSalt());
            $user->setPassword($encodedPass);
            if($userProfile->getIndividualized()){
                $userProfileTags = $user->getFreeProfile();
                if(!$userProfileTags){
                    $userProfileTags = new UserProfileByTags();
                }
                else{
                    $userProfileTags->removeProfileTags();
                }
                $userProfileTags->setProfileActive(true);
                $userProfileTags->setUser($user);
                $profileTags = $reqAssoc['tags'];
                foreach($profileTags as $tagId){
                    $tag = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\Tag', $tagId);
                    if($tag){
                        $userProfileTags->addProfileTag($tag);
                    }
                }
                $this->getDoctrine()->getManager()->persist($userProfileTags);
                $user->setFreeProfile($userProfileTags);
            }
            else{
                $userProfile->setIndividualized(false);
                $existingUserProfileByTags = $user->getFreeProfile();
                if($existingUserProfileByTags){
                    $existingUserProfileByTags->setProfileActive(false);
                    $this->getDoctrine()->getManager()->persist($existingUserProfileByTags);
                }
                $this->getDoctrine()->getManager()->persist($userProfile);
                $this->getDoctrine()->getManager()->flush();
            }
            $userManager->updateUser($user);
            return parent::getJsonForData(array('success' => 1, 'token' => $user->getSalt(), 'profileid' => $profileId));
        } else {
            return parent::getJsonForData(array('success' => 0, 'msg' => 'Der er allerede en bruger registreret med den angivne e-mail og eller brugernavn. Brug venligst en anden e-mail adresse eller brugernavn. Tak.'));
        }
        return parent::getJsonForData(array('failed' => 1));
    }


    /**
     * @Route("user/edit")
     * @Method("POST")
     */
    public function editJSONAction(Request $request){
    $success = 0;
    $user=null;
         if(parent::getIsJSON()){
            if(parent::getTokenPassed($request)){
                $reqAssoc = parent::getRequestFromJSON($request);
                $userManager = $this->container->get('fos_user.user_manager');
                /**
                 * @var User $user
                 */
                $user = parent::getUser();
                if(isset($reqAssoc['username']) && ($user->getUsername() !== $reqAssoc['username'])){
                    if($userManager->findUserByUsername($reqAssoc['username'])){
                        return parent::getJsonForData(array('success' => 0, 'msg'=>'Brugernavnet er allerede anvendt af en bruger. Angiv venligst et andet','token' => $user->getSalt()));
                    }
                }

                if(isset($reqAssoc['email']) && ($user->getEmailCanonical() !== $reqAssoc['email'])){
                    if($userManager->findUserByEmail($reqAssoc['email'])){
                        return parent::getJsonForData(array('success' => 0, 'msg'=>'E-mail adressen er allerede anvendt af en bruger. Angiv venligst et andet','token' => $user->getSalt()));
                    }
                }

                $username = (isset($reqAssoc['username']) && !$userManager->findUserByUsername($reqAssoc['username']))?$reqAssoc['username']:$user->getUsername();
                $user->setUsername($username);
                $email = (isset($reqAssoc['email']) && !$userManager->findUserByEmail($reqAssoc['email']))?$reqAssoc['email']:$user->getEmail();
                $user->setEmail($email);
                $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
                $password = isset($reqAssoc['password'])?$encoder->encodePassword($reqAssoc['password'], $user->getSalt()):$user->getPassword();
                $user->setPassword($password);
                $newsletter = 'unset';
                $contact = 'unset';
                if(isset($reqAssoc['newsletter'])){
                    $newsletter = ($reqAssoc['newsletter']==1)?true:false;
                }
                if(isset($reqAssoc['contactme'])){
                    $contact = ($reqAssoc['contactme']==1)?true:false;
                }
                if($newsletter !== 'unset'){
                    $user->setNewsletter($newsletter);
                }
                if($contact !== 'unset'){
                    $user->setContact($contact);
                }

                if(isset($reqAssoc['profileid'])){
                $profileId = (($reqAssoc['profileid']!==''))?intval($reqAssoc['profileid']):$user->getProfile()->getId();
                $profile = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\TvguserProfile', $profileId);
                $user->setProfile($profile);
                // If the selected profile needs to have its place-properties selected individually
                if($profile->getIndividualized()){
                    $userProfileTags = $user->getFreeProfile();
                    if(!$userProfileTags){
                      $userProfileTags = new UserProfileByTags();
                    }
                    else{
                        $userProfileTags->removeProfileTags();
                    }
                    $userProfileTags->setProfileActive(true);
                    $userProfileTags->setUser($user);
                    $profileTags = $reqAssoc['tags'];
                    foreach($profileTags as $tagId){
                        $tag = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\Tag', $tagId);
                        if($tag){
                            $userProfileTags->addProfileTag($tag);
                        }
                    }
                    $this->getDoctrine()->getManager()->persist($userProfileTags);
                    $user->setFreeProfile($userProfileTags);
                }
                else{
                    $profile->setIndividualized(false);
                    $existingUserProfileByTags = $user->getFreeProfile();
                    if($existingUserProfileByTags){
                      $existingUserProfileByTags->setProfileActive(false);
                      $this->getDoctrine()->getManager()->persist($existingUserProfileByTags);
                    }
                    $this->getDoctrine()->getManager()->persist($profile);
                }
                }
                $userManager->updateUser($user);
                return parent::getJsonForData(array('success' => 1, 'token' => $user->getSalt(), 'numtags'=>count($profileTags)));
            }
          }
    return parent::getJsonForData(array('success' => 0, 'token' => $user->getSalt()));
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
        $msg=null;
        if ($user) {
            $em = $this->getDoctrine()->getManager();
            $userTmpExisting = $em->getRepository('Gladtur\TagBundle\Entity\UserPassword')->findOneBy(
                array('user' => $user->getId())
            );
            if ($userTmpExisting) {
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
            $msg = 'Der findes ingen bruger med det angivne brugernavn eller e-mail adresse!';
        }
        if(($response == 1) && $atomic){
            // Send the email automatically!
            return $this->sendForgotpasswordEmail($usernameoremail);
        }
        else{
            return parent::getJsonForData(array('success' => $response, 'msg' => $msg));
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
        $msg = 'Der findes ingen bruger med det angivne brugernavn eller e-mail adresse!';
        $user = $userManager->findUserByUsernameOrEmail($usernameOrEmail);
        if ($user) {
            $msg = null;
            $message = Swift_Message::newInstance()
                ->setSubject('Gladtur - Nyt kodeord til dig!')
                ->setFrom('noreply@gladtur.dk')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'GladturMobileBundle:Default:email_forgottenpassword.txt.twig',
                        array(
                            'username' => $user->getUsername(),
                            'devicelink' => urlencode('gladtur://gladtur.dk/mobile/setpassword?u='.$usernameOrEmail.'&q='.md5($usernameOrEmail.$user->getUsername())),
                            'link' => 'http://gladtur.dk/mobile/setpassword?u='.$usernameOrEmail.'&q='.md5($usernameOrEmail.$user->getUsername()).'&p=d',
                        )
                    )
                );
            $response = @$this->get('mailer')->send($message);
            if ($response) $response = 1;
        }

        return parent::getJsonForData(array('success' => $response, 'msg'=>$msg));
    }

    /**
     * @Route("setpassword")
     */
    public function setPasswordAction(Request $request)
    {
        $usernameoremail = $this->getRequest()->get('u', null);
        $secretcode = $this->getRequest()->get('q', null);
        $platform = $this->getRequest()->get('p', ''); // 'd' is for desktop, NOT device!!!
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
        if($userTmpExisting && ($userTmpExisting->getNewpasswordToken() == $secretcode)){
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
            $userToken = $user->getSalt();
        }
        }
        // Device app making the request, and getting the response!
        if($platform !== 'd'){
          return parent::getJsonForData(array('success' => $response, 'token'=>$userToken, 'profileid'=>$user->getProfile()->getId()));
        }
        else{
            return $this->render(
                'WebsiteBundle:User:password_reset.html.twig',
                array('success' => $response)
            );
        }
    }

    /**
     * @Route("deleteuser/{usertoken}")
     * @Method({"GET", "POST"})
     */
    public function deleteUserAction($usertoken){
        $success = 0; // User does not exist, or is soft-deleted! //
        /*$username = $this->getRequest()->get('username', null);
        $userId = $this->getRequest()->get('id', null);*/
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserBy(array('salt'=>$usertoken));
        /*if($username){
            $user = $userManager->findUserByUsernameOrEmail($username);
        }
        if($userId){
            $user = $userManager->findUserBy(array('id'=>$userId));
        }
        */
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
     * @Method({"GET", "POST"})
     */
    public function undeleteUserAction(){
        $em = $this->getDoctrine()->getManager();
        $filters = $em->getFilters();
        //$filters->disable('softdeleteable');
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
     * @Route("login")
     * @Method({"GET", "POST"})
     */
    public function loginAction(Request $request){
        if(parent::getIsJSON()){
       // $requestContent = parent::getRequestFromJSON($request);
            $requestContent = $request->getContent();
            /** @var Serializer $serializer **/
            //$content = str_replace("'", '"', $content);
            $actionName = $request->attributes->get('_controller');
            $actionName = str_replace(array('::', '\\'), array('-', '-'), $actionName);
            //file_put_contents('/symftemp/'.$actionName.'__at_'.date('d-m-h_i_s').'__raw.txt', $requestContent);
            $requestContent = substr($requestContent,1);
            $requestContent = substr($requestContent,0,-1);
            $requestContent = '{'.$requestContent.'}';
            $requestContent = json_decode($requestContent, true);
        if(!isset($requestContent['username']) || !isset($requestContent['password'])) return parent::getJsonForData(array('success' => 0));
        $um = $this->container->get('fos_user.user_manager');
        $user = $um->findUserBy(array('username'=>$requestContent['username']));
        if($user){
            $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
            $encodedUserRefPass = $user->getPassword();
            $encodedReqPass = $encoder->encodePassword($requestContent['password'], $user->getSalt());
            if($encodedReqPass == $encodedUserRefPass) return parent::getJsonForData(array('success' => 1, 'token'=>$user->getSalt(), 'profileid'=>$user->getProfile()->getId()));
        }
        }
        return parent::getJsonForData(array('success' => 0));
    }

    /**
     * @param Request $request
     * @Route("user/detail/{usertoken}")
     * @Method({"GET", "POST"})
     */
    public function userDetailAction($usertoken){
        $um = $this->container->get('fos_user.user_manager');
        /**
         * @var User $user
         */
        $profilename = 'Ikke valgt!';
        $user = $um->findUserBy(array('salt'=>$usertoken));
        if(!$user) return parent::getJsonForData(array('success' => 0));
        if($user->getProfile() && ($user->getProfile()->getReadableName() !== '')){
            $profilename = $user->getProfile()->getReadableName();
            $profile_icon_path = 'http://gladtur.dk'.'/uploads/avalanche/thumbnail/icons/profiles/'.$user->getProfile()->getPath();
        }
        $userData = array('username'=>$user->getUsername(), 'email'=>$user->getEmail(), 'newsletter'=>($user->getNewsletter())?1:0, 'contactme'=>($user->getContact())?1:0,'profileid' => $user->getProfile()->getId(),'profilename'=>$profilename, 'last_login'=>$user->getLastLogin(), 'icon' => $profile_icon_path);
        $userTags = array();
        if($user->getFreeProfile() && $user->getFreeProfile()->getProfileActive()){
            $userTagEntities = $user->getFreeProfile()->getProfileTags();
            foreach($userTagEntities as $tag){
                $userData['tags'][] = $tag->getId();
            }
        }
        return parent::getJsonForData(array('success' => $userData));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("user/editjson", name="json_user_edit")
     * @Method({"GET", "POST"})
     */
    public function editUserAction(Request $request){
        if(parent::getIsJSON() && parent::getTokenPassed($request)){
            return parent::getJsonForData(array('success' => 1, 'token'=>parent::getUser()->getSalt()));
        }
        return parent::getJsonForData(array('success' => 0));
    }
}