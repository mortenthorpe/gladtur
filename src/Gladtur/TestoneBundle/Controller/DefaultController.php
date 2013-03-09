<?php

namespace Gladtur\TestoneBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * TestOne controller.
 *
 * @Route("/")
 */

class DefaultController extends Controller
{
    /**
     * @Route("", name="test")
     * @Template("GladturTestoneBundle:Default:list.html.twig")
     */
    public function indexAction()
    {
		return $this->render('GladturTestoneBundle:Default:index.html.twig');
	}
    /**
     * @Route("steder", name="list")
     * @Template()
     */
    public function listAction()
    {
        return $this->render('GladturTestoneBundle:Location:list.html.twig');
    }

    /**
     * @Route("sted/{id}", name="sted")
     * @Template("GladturTestoneBundle:Location:location.html.twig")
     */
    public function locationAction($id)
    {
        $emLocation = $this->getDoctrine()->getManager();

        $location = $emLocation->getRepository('GladturTagBundle:Location')->find($id);

        if (!$location) {
            throw $this->createNotFoundException('Unable to find Location entity.');
        }

        $emLocation_tags = $this->getDoctrine()->getManager();

        $location_tags = $emLocation_tags->getRepository('GladturTagBundle:Tag')->findAll();
		
		$location_map_active_tags=array();
		$location_map_active_tags[6] = array(1,3,4,6,7,8,12,15,18,33,42,44);
		$location_map_active_tags[2] = array(3,10,13,16,19,33,43,44);
		$location_map_active_tags[4] = array(4,6,8,12,15,18,21,42,44);
		$location_map_active_tags[7] = array(6,7,8,12,15,18,13,33,44,42);
		$location_map_active_tags[8] = array(10,13,19,43,44);
		$location_map_active_tags[9] = array(10,13,19,43,44);
        return array(
            'location'      => $location,
			'location_tags' => $location_tags,
			'activetags' => $location_map_active_tags[$id],
        );
		//return $this->render('GladturTestoneBundle:Location:location.html.twig');
    }
	
    /**
     * @Route("sted/{id}/info", name="stedinfo")
     * @Template("GladturTestoneBundle:Location:info.html.twig")
     */
    public function infoAction($id)
    {
        $emLocation = $this->getDoctrine()->getManager();

        $location = $emLocation->getRepository('GladturTagBundle:Location')->find($id);
        return array(
            'location'      => $location,
        );
    }
	
    /**
     * @Route("stedtag/{ids}/info", name="stedtaginfo")
     * @Template("GladturTestoneBundle:Location:locationtaginfo.html.twig")
     */
    public function stedtaginfoAction($ids)
    {
		$ids=explode(',',$ids);
        $emTag = $this->getDoctrine()->getManager();
		$tags=array();
		foreach($ids as $id){
        	$tags[] = $emTag->getRepository('GladturTagBundle:Tag')->find($id);
		}
        return array(
            'tags'      => $tags,
        );
    }
}
