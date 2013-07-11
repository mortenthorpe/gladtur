<?php

namespace Gladtur\MobileBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    //private $serializer;
    private $authAssoc;
    protected $container;

    public function __construct(){
        $this->authAssoc = array('username' => '', 'pass' => '', 'token' => null);
    }
    /**
     * @Route("/mobile", name="mobile_location_listing")
     * @Template()
     */
    public function indexAction()
    {
        $locations = array(1=>array('name' => 'Meyers Madhus', 'categories' => array('top'=>'Restaurant', 'subs'=>array('Mad & spise', 'Café'), 'properties' => array('Toilet', 'Parkering' => array('Personbil', 'H-bil', 'Varevogn')))), 3=>array('name' => 'Hatoba Sushi', 'categories' => array('top'=>'Restaurant', 'subs'=>array('Mad & spise'), 'properties' => array('Toilet', 'Parkering' => array('Personbil', 'H-bil', 'Varevogn')))));
        $response=new \Symfony\Component\HttpFoundation\JsonResponse($locations);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/mobile/location/{id}", name = "mobile_location_show")
     * @Template()
     */
    public function locationShowAction($id)
    {
        $locations = array(1=>array('name' => 'Meyers Madhus', 'categories' => array('top'=>'Restaurant', 'subs'=>array('Mad & spise', 'Café'), 'properties' => array('Toilet', 'Parkering' => array('Personbil', 'H-bil', 'Varevogn')))), 3=>array('name' => 'Hatoba Sushi', 'categories' => array('top'=>'Restaurant', 'subs'=>array('Mad & spise'), 'properties' => array('Toilet', 'Parkering' => array('Personbil', 'H-bil', 'Varevogn')))));
        $location = $locations[intval($id)];
        $response=new \Symfony\Component\HttpFoundation\JsonResponse($location);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
