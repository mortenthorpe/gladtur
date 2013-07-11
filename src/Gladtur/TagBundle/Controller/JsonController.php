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

class JsonController extends Controller {
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
     */
    public function getRequestFromJSON(Request $request){
        $params = array();
        $content = $request->getContent();
        /** @var Serializer $serializer **/
        $content = str_replace("'", '"', $content);
        $content = substr($content,1);
        $content = substr($content,0,-1);
        $content = '{'.$content.'}';
        $params = json_decode($content, true); // 2nd param to get as array
        return $params;
    }
}