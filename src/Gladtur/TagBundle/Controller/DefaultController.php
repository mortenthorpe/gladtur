<?php

namespace Gladtur\TagBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\ClassLoader\UniversalClassLoader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;


class DefaultController extends Controller
{
    public function indexAction()
    {
		$menuItems = array('tagIndex' => '@tag', 'location_tagIndex' => '@location_tag', 'locationIndex' =>'@location', 'tag_categoryIndex' => '@tagcategoryIndex' );
        return $this->render('GladturTagBundle:Default:index.html.twig', array('menuItems' => $menuItems));
    }

    /**
     * @Route("testimage");$
     **/
    public function testImageAction(Request $request){
        $imagemanagerResponse = $this->container
            ->get('liip_imagine.controller')
            ->filterAction(
                $this->getRequest(),
                'test/image1.png',      // original image you want to apply a filter to
                'thumbnail'              // filter defined in config.yml
            );
        return $this->render('GladturTagBundle:Default:testImage.html.twig');
    }

    /**
     * @param $locationId
     * @Route("score/{locationId}/{profileId}")
     */
    public function testLocationScore($locationId, $profileId){
        $location = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\Location', $locationId);
        $userprofile = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\TvguserProfile', $profileId);
        return new Response('The scores are IN!: '.$this->getDoctrine()->getManager()->getRepository('Gladtur\TagBundle\Entity\Location')->getScoreval($location, $userprofile));
    }

    /**
     * @param $tagId
     * @param $locationId
     * @return Reponse
     * @Route("tagvalue/{tagId}/{locationId}")
     */
    public function getLocationTagValue($tagId, $locationId){
        $location = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\Location', $locationId);
        $rs=$this->getDoctrine()->getManager()->getRepository('Gladtur\TagBundle\Entity\UserLocationTagData')->getLocationTagValue($tagId, $location, 2);
        //$rs = $rs[0]['tagvalue'];
        return new Response('The value for tag-ID: ' . $tagId . '@Location-ID: ' . $locationId .'is: '.$rs);
    }

    /**
     * @Route("solrtest")
     */
    public function testSolr(){
        $location = $this->getDoctrine()->getManager()->find('\Gladtur\TagBundle\Entity\Location', 58);
        $location->testEncoding();
        return new Response(mb_strtolower(str_replace(array('Æ','Ø','Å','æ','ø','å','\u00c6','\u00d8','\u00c5','\u00e6','\u00f8','\u00e5'), array('AE','OE','AA','ae','oe','aa','AE','OE','AA','ae','oe','aa'), $location->getReadableName())) . mb_strtolower(str_replace(array('Æ','Ø','Å','æ','ø','å','\u00c6','\u00d8','\u00c5','\u00e6','\u00f8','\u00e5'), array('AE','OE','AA','ae','oe','aa','AE','OE','AA','ae','oe','aa'), $location->getTopCategory()->getReadableName())));
    }

    /**
     * @Route("testlocs/{topcatid}/{lat}/{lon}")
     * @return JsonResponse
     */
    public function testGetLocationDistanced($topcatid, $lat, $lon){
        $locationsDistances = $this->getDoctrine()->getManager()->getRepository('Gladtur\TagBundle\Entity\Location')->getPlacesOrderByDistance($lat, $lon, ($topcatid>0)?$topcatid:null, 0, 20, $orderDir = 'ASC');
        return new JsonResponse($locationsDistances);
    }
}
