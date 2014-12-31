<?php
/**
 * Created by PhpStorm.
 * User: mortenthorpe
 * Date: 05/09/14
 * Time: 09.23
 */

namespace Gladtur\TagBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;


class PageloadStatsListener{

    public function onKernelController(FilterControllerEvent $event)
    {
            if ($event->getController() instanceof EventLoggableInterface) {
                $event->getRequest()->getSession()->set('positionKnown', true);
                $user = ($event->getRequest()->getUser())?$event->getRequest()->getUser():$this->api_user;
                $userLat = $event->getRequest()->getSession()->get('ulat', 55.675283);
                $userLong = $event->getRequest()->getSession()->get('ulng', 12.570163);
                // $event->getRequest()->query->get('token')
            }
    }

    public function _mytmp(){
$now = time();
$secured = false;
if($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
$secured = 'IS_AUTHENTICATED_FULLY';
}
$request = $this->getRequest();
$userAgent = $request->headers->get('User-Agent');
$request->setTrustedProxies(array('127.0.0.1'));
$clientIP = $request->getClientIp();
// the URI being requested (e.g. /about) minus any query parameters
$pathinfo = $request->getPathInfo();
$method = $request->getMethod();
$getParams = $request->query->all();
foreach($getParams as $paramName => $paramValue){
    if($paramName == 'locationid'){
        $location = $this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\Location')->find($paramValue);
        $getParams['location_by_id'] = $location->getSlug() . ' (By ID: ' . $paramValue. ')';
    }
}
// $request->request->all() works for content type: application/x-www-form-urlencoded, so no result for application/json
$postParams = $request->request->all();
// $request->getContent() works for application/json, and NOT for application/x-www-form-urlencoded
$jsonPostParams = json_decode($request->getContent(), true);
if(isset($jsonPostParams['token'])) $secured = 'token';
$totalrequest = array('unixtime'=>$now, 'date (d/m/Y)' => date("d/m/Y", $now),'time'=>date("H:i:s", $now), 'IP'=>$clientIP, 'User-Agent'=>$userAgent, 'controller'=>$request->attributes->get('_controller'),'path'=>$pathinfo, 'method' => $method, 'getParams' => $getParams, 'postParams' => $postParams, 'jsonPostParams' => $jsonPostParams, 'secured' => $secured);
//return new JsonResponse($totalrequest);
    }
}