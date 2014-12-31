<?php
/**
 * Created by PhpStorm.
 * User: mortenthorpe
 * Date: 24/07/14
 * Time: 10.07
 */

namespace Gladtur\TagBundle\Controller;

use DateTime;
use Doctrine\ORM\Query\ResultSetMapping;
use Gladtur\TagBundle\Entity\EventLogger;
use Gladtur\TagBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class GladTurStatsController extends Controller{
    private $dump_called;
    private $timestamp;
    private $time_gmt_offset;
    private $uri;
    private $format;
    private $name;
    private $arguments;
    private $method;
    private $user;
    private $client;
    private $requestData;

    protected $container;
    /**
     * Constructor.
     *
     * @param array $data An array of key/value parameters.
     *
     * @throws \BadMethodCallException
     */
   /* public function __construct($container)
    {
        $this->container = $container;
        $request = $this->container->get('request');
        $requestGetData = $request->query->all();
        $requestPostData = $request->request->all();
        $requestAttributesAll = $request->attributes->all();
        $requestContent = array('content' => $request->getContent());
        $this->requestData = array_merge($requestGetData, $requestPostData, $requestAttributesAll, $requestContent);
        $this->timestamp = time();
        $this->time_gmt_offset = date('Z');
        $this->uri = $request->getRequestUri();
        $this->format = $request->getRequestFormat();
        $this->setName($request->get('_route'), false);
        $this->arguments = array();
        $this->method = '';
        $this->user = null;
        $this->client = array($request->getClientIp());
    }
*/
    private function collectData(){
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
    }
    /**
     * @param array $arguments
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param array $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @return array
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $name
     */
    public function setName($name, $dumpNow = true)
    {
        $this->name = $name;
        if($dumpNow){
          $this->dump();
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param \Gladtur\TagBundle\Entity\User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return \Gladtur\TagBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param string $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    public function dump(){
        $loggedEvent = new EventLogger();
        // The user originates from the request from the mobile JSON data 'token'
        if(isset($requestContent['token'])){
            $um = $this->container->get('fos_user.user_manager');
            $this->user = $um->findUserBy(array('salt'=>$requestContent['token']));
            unset($requestContent['token']);
        }
        else{
            $this->user = $this->container->get('security.context')->getToken()->getUser();
        }
        if($this->user instanceof User){
            $loggedEvent->setUser($this->user);
        }
        $loggedEvent->setRequestString($this->getName(). '@@@' .json_encode($this->requestData));
        $this->getDoctrine()->getManager()->persist($loggedEvent);
        $this->getDoctrine()->getManager()->flush();
    }

    /**
     * @Route("stats", name="admin_allstats")
     * @Template("GladturTagBundle:Stats:all.html.twig")
     */
    public function getStatsAction(Request $request){
        // Test
        $userAgent = $this->get('gladtur.stats.uaparser')->parse($request->headers->get('User-Agent'));
        // ./Test

        $dateField = $request->get('dateformat', 'date'); // The symfony form widget date-type; 'date' or 'datetime'
        $dateFormat = 'Y-m-d h:i:s';
        $date_start_timestamp = strtotime("last month");
        $date_end_timestamp = time();
        $date_start_timestamp_prev = $date_start_timestamp - ($date_end_timestamp-$date_start_timestamp);
        $date_end_timestamp_prev = $date_start_timestamp;
        $date_start = $request->get('sdate', new DateTime(date($dateFormat, strtotime("last month"))));
        $date_end = $request->get('edate',  new DateTime(date($dateFormat, time())));
        if(intval(date('Y')) > 2014){
          $widget_years = range(intval(date('Y'))-1,intval(date('Y')));
        }
        else{
            $widget_years = array(2014);
        }

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('count', 'count');
        $general_allUsers = $this->getDoctrine()->getManager()->createNativeQuery("select count(u.id) as count FROM fos_user u", $rsm)->getSingleScalarResult();
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('count', 'count');
        try {
            $general_newUsers = $this->getDoctrine()->getManager()->createNativeQuery(
                "select count(u.id) as count FROM fos_user u where u.created > " . $date_start_timestamp . " and u.created <=" . $date_end_timestamp,
                $rsm
            )->getSingleScalarResult();
        }
        catch(\Doctrine\Exception\NoResultException $e){
            $general_newUsers = 0;
        }
        try {
            $general_newUsers_prev = $this->getDoctrine()->getManager()->createNativeQuery(
                "select count(u.id) as count FROM fos_user u where u.created > " . $date_start_timestamp_prev . " and u.created <=" . $date_end_timestamp_prev,
                $rsm
            )->getSingleScalarResult();
        }
        catch(\Doctrine\Exception\NoResultException $e){
            $general_newUsers_prev = 0;
        }
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('count', 'count');
        $general_allLocations = $this->getDoctrine()->getManager()->createNativeQuery("select count(l.id) as count FROM location l", $rsm)->getSingleScalarResult();
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('count', 'count');
        $general_publishedLocations = $this->getDoctrine()->getManager()->createNativeQuery("select count(l.id) as count FROM location l WHERE l.published=1", $rsm)->getSingleScalarResult();
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('count', 'count');
        try {
            $period_maxcreatedLocationsCount = $this->getDoctrine()->getManager()->createNativeQuery(
                "select count(l.id) as count FROM location l WHERE l.published=1 and l.created_by_id is not null GROUP BY l.created_by_id order by count DESC limit 1",
                $rsm
            )->getSingleScalarResult();
        }
        catch(\Doctrine\ORM\NoResultException $e) {
            $period_maxcreatedLocationsCount = 0;
        }
        try {
            $general_newUsersUnsubscribed = $this->getDoctrine()->getRepository(
                'Gladtur\TagBundle\Entity\EventLogger'
            )->findBy(array('e_action' => 'user_unsubscribe'));
        }
        catch(\Doctrine\ORM\NoResultException $e) {
            $general_newUsersUnsubscribed = 0;
        }
        try {
            $general_userLocationMaxVote = $this->getDoctrine()->getManager()->createNativeQuery("select count(ultd.id) as count from user_location_tag_data ultd where ultd.created BETWEEN " . $date_start_timestamp . " and " . $date_end_timestamp . " group by ultd.user_id, ultd.location_id order by count DESC limit 1", $rsm)->getSingleScalarResult();
        }
        catch(\Doctrine\ORM\NoResultException $e) {
            $general_userLocationMaxVote = 0;
        }

        try {
            $general_userLocationMaxVote_prev = $this->getDoctrine()->getManager()->createNativeQuery(
                "select count(ultd.id) as count from user_location_tag_data ultd where ultd.created BETWEEN " . $date_start_timestamp_prev . " and " . $date_end_timestamp_prev . " group by ultd.user_id, ultd.location_id order by count DESC limit 1",
                $rsm
            )->getSingleScalarResult();
        }
catch(\Doctrine\ORM\NoResultException $e) {
    $general_userLocationMaxVote_prev = 0;
    }

        $general_newUsersUnsubscribed = 0;
        $dateForm = $this->createFormBuilder()->add('sdate', $dateField, array('years'=>$widget_years, 'label'=>'Dato-fra', 'input'=>'datetime', 'widget'=>'choice', 'data'=>$date_start))->add('edate', $dateField, array('years'=>$widget_years,'label'=>'Dato-til', 'input'=>'datetime', 'widget'=>'choice', 'data'=>$date_end))->getForm()->createView();
        return array(
            // http://yzalis.github.io/UAParser/
            // Sensible, readable precise UserAgent result below...
            'user_agent' => $userAgent->getOperatingSystem()->getFamily() . '(' . $userAgent->getOperatingSystem()->getMajor().'.'. $userAgent->getOperatingSystem()->getMinor() . ')' . ', ' . $userAgent->getBrowser()->getFamily().' (v. '.$userAgent->getBrowser()->getMajor().')',
            'dateform' => $dateForm,
            'general_allUsers' => $general_allUsers,
            'general_newUsers' => $general_newUsers,
            'general_newUsers_prev' => $general_newUsers_prev,
            'general_newUsersUnsubscribed' => $general_newUsersUnsubscribed,
            'general_allLocations' => $general_allLocations,
            'general_publishedLocations' => $general_publishedLocations,
            'general_userLocationMaxVote' => $general_userLocationMaxVote,
            'general_userLocationMaxVote_prev' => $general_userLocationMaxVote_prev,
            'period_maxcreatedLocationsCount' => $period_maxcreatedLocationsCount,
        );
    }


    private function _parseUserAgentString($userAgentString){
        $matchA = array();
        $mobile_api = (strpos($userAgentString, 'Glad Tur') !== false)?true:false;
        if($mobile_api) {
            preg_match('/^.*?\/(\S+) \((\S+?); (\S+) (\S+?); (.*?)\)$/i', $userAgentString, $matchA);
            return array(
                'recognized' => true,
                'mobile' => $mobile_api,
                'version' => $matchA[1],
                'engine' => 'application',
                'device' => $matchA[2],
                'platform' => $matchA[3],
                'platform_version' => $matchA[4],
                'data' => $userAgentString,
            );
        }
        else{
            // Generic User-Agent, typically for any web-browser
            // Tested on Mac OS X, matches in: Safari, Chrome, Firefox,
            //preg_match('/^(\S+) \((\S+?); (.*?)\).*?\(?.*?\)?.*?(\S+?)\/(\S+?) (\S+?)$/i', $userAgentString, $matchA);
            // more generic:
            //preg_match('/^(\S+) \(([^;]+); (.*?)\).*?\(?.*?\)?.*?(\S+?)\/(\S+?) (\S+?)$/i', $userAgentString, $matchA);
            // Specific user-agent mapping left to do OR to google analytics capture
                return array(
                    'recognized' => false,
                    'mobile' => $mobile_api,
                    'data' => $userAgentString,
                );
        }
    }
} 