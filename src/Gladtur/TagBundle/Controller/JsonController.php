<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 6/10/13
 * Time: 9:49 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Gladtur\TagBundle\Controller;

use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class JsonController extends Controller{

    private $tokenPassed;
    public $user;

    public function __construct(){
        $this->setTokenPassed(false);
        $this->user = null;
    }

    public function getUser(){
        return $this->user;
    }
    /**
     * @param boolean $tokenPassed
     */
    public function setTokenPassed($tokenPassed)
    {
        $this->tokenPassed = $tokenPassed;
    }

    /**
     * @return boolean
     */
    public function getTokenPassed(Request $request)
    {
        if($this->tokenPassed) return true;
        if($this->getIsJSON()){
            $params = array();
            $content = $request->getContent();
            //$content = str_replace("'", '"', $content);
            $content = substr($content,1);
            $content = substr($content,0,-1);
            $content = '{'.$content.'}';
            $content = json_decode($content, true); // 2nd param - decode to array, not to stdClass() //
            if(isset($content['token'])){
                $um = $this->container->get('fos_user.user_manager');
                $user = $um->findUserBy(array('salt'=>$content['token']));
                unset($content['token']);
                if($user){
                    if($user->isEnabled()){
                        $this->user = $user;
                        $this->setTokenPassed(true);
                    }
                }
            }
        }
        return $this->tokenPassed;
    }


    public function getIsJSON(){
        if (0 === strpos($this->getRequest()->headers->get('Content-Type'), 'application/json')) return true;
        return false;
    }

    /**
     * @param mixed $data
     * @return JsonResponse
     */
    public function getJsonForData($data = array()){
        /*$response=new JsonResponse($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;*/
        /** @var Serializer $serializer **/
        $serializer = $this->container->get('jms_serializer');
        /**
         * @var $serializer
         */
        $response=new Response($serializer->serialize($data, 'json'));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @param Request $request
     * @return array|mixed
     * @Method({"POST"})
     */
    public function getRequestFromJSON(Request $request){
        if(!$request) $request = $this->getRequest();
        $params = array();
        if($this->getTokenPassed($request)){
        $content = $request->getContent();
        /** @var Serializer $serializer **/
        //$content = str_replace("'", '"', $content);
        $content = substr($content,1);
        $content = substr($content,0,-1);
        $content = '{'.$content.'}';
        $params = json_decode($content, true); // 2nd param - decode to array, not to stdClass() //
        }
        return $params;
    }

    /**
     * @param Request $request
     * @return array|mixed
     * @Method({"POST"})
     */
    public function getUnauthRequestFromJSON(Request $request){
        if(!$request) $request = $this->getRequest();
        $params = array();
        $content = $request->getContent();
        $actionName = $request->attributes->get('_controller');
        $actionName = str_replace(array('::', '\\'), array('-', '-'), $actionName);
        //file_put_contents('/symftemp/'.$actionName.'__at_'.date('d-m-h_i_s').'__raw.txt', $content);
        $content = substr($content,1);
        $content = substr($content,0,-1);
        $content = '{'.$content.'}';
        $params = json_decode($content, true); // 2nd param - decode to array, not to stdClass() //
        return $params;
    }
}