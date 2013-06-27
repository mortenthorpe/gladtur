<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 6/12/13
 * Time: 8:32 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Gladtur\MobileBundle\Controller;

use Gladtur\TagBundle\Controller\JsonController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use \Doctrine\Common\Collections\Criteria;

class LocationController extends JsonController
{

    /**
     * @Route("locations/profileid/{profileid}/catid/{catid}/lat/{lat}/lon/{lon}", defaults={"lat" = 0, "lon" = 0})
     */
    public function indexAction()
    {
        /** @var EntityManager $em */
        /*$em = $this->getDoctrine()->getManager();
        //$entities = $em->getRepository('GladturTagBundle:TvguserProfile')->createQueryBuilder('TvguserProfile')->getQuery()->getResult();
        /** @var EntityRepository $entities */
        /*$childcategoriesCriteria=Criteria::create()->where(Criteria::expr()->eq("parentCategory", null))->orderBy(array('id'));
        $entities = $em->getRepository('GladturTagBundle:LocationCategory')->findAll();//matching($childcategoriesCriteria);
        return parent::getJsonForData($entities);*/
        $locationsData = array(
            array(
                'id' => 1,
                'topcatid' => 10,
                'name' => 'Meyers Madhus',
                'lat' => 0,
                'lon' => 0,
                'distance' => -1
            ),
            array(
                'id' => 2,
                'topcatid' => 10,
                'name' => 'Sushitarian',
                'lat' => 55.963901,
                'lon' => 12.281552,
                'distance' => 235
            )
        );

        return parent::getJsonForData($locationsData);
    }

    /**
     * @Route("/locations_profile/profileid/{profileid}/catid/{catid}/lat/{lat}/lon/{lon}", defaults={"lat" = 0, "lon" = 0})
     */
    public function nearbyplacesAction()
    {
        $em = $this->getDoctrine()->getManager();
        //$entities = $em->getRepository('GladturTagBundle:TvguserProfile')->createQueryBuilder('TvguserProfile')->getQuery()->getResult();
        /** @var EntityRepository $entities */
        /*$childcategoriesCriteria=Criteria::create()->where(Criteria::expr()->eq("parentCategory", null))->orderBy(array('id'));
        $entities = $em->getRepository('GladturTagBundle:LocationCategory')->findAll();//matching($childcategoriesCriteria);*/
        $locationsData = array(
            array(
                'id' => 1,
                'topcatid' => 10,
                'name' => 'Meyers Madhus',
                'lat' => 55.85,
                'lon' => 12.275,
                'distance' => 690
            ),
            array(
                'id' => 2,
                'topcatid' => 10,
                'name' => 'Sushitarian',
                'lat' => 55.963901,
                'lon' => 12.281552,
                'distance' => 235
            )
        );

        return parent::getJsonForData($locationsData);
    }

    /**
     * @Route("/location/locationid/{locationid}/profileid/{profileid}")
     */
    public function placeDetailAction()
    {
        $locationDetailData = array(
            'name' => 'Meyers Madhus',
            'lat' => 55.85,
            'lon' => 12.275,
            'distance' => 690,
            'topcat' => array('id' => 10, 'name' => 'Restaurant'),
            'subcategories' => array(array('id' => 1, 'name' => 'Cafe'), array('id' => 2, 'name' => 'Gadekøkken')),
            'place_images' => array(
                array('delta' => 0, 'URL' => 'http://images.apple.com/home/images/ios_title.png'),
                array('delta' => 1, 'URL' => 'http://images.apple.com/home/images/ios_title.png')
            ),
            'address' => array('zip' => '1705 KBH K', 'streetname' => 'Gl. Kongevej 56, stuen'),
            'opening_hours' => array('open' => '09:00', 'close' => '18:00'),
            'phone' => '+45 12 34 56 78',
            'email' => 'claus@meyers.dk',
            'website' => 'http://meyers.dk',
            'description' => 'Meyers Madhus. Spis her, eller tag med hjem. Dagligt gadekøkken med hovedretter der mætterde fleste fra Kr. 69 til 135',
            'contact_person' => 'Claus Meyer',
            'properties' => array(
                array(
                    'id' => 1,
                    'name' => 'Niveaufri adgang',
                    'info' => 'Niveaufri adgang betyder at der slet ikke er trapper, eller at trapperne kan omgåes ved rampe til f.eks. rullestol',
                    'value' => 1
                )
            )
        );

        return parent::getJsonForData($locationDetailData);
    }

    /**
     * @Route("/location/locationid/{locationid}/comments")
     */
    public function placeComments($locationid)
    {
        $locationCommentData = array(
            array(
                'profileid' => 1,
                'timestamp' => 1371719972,
                'comment_txt' => 'A dummy-comment, no. 1 in the sequence',
                'comment_images' => array(array('URL' => 'http://www.morning.dk/morning.hyperesources/morningnetwork-webV2.png'))
            ),
            array(
                'profileid' => 2,
                'timestamp' => 1371719972,
                'comment_txt' => 'A dummy-comment, no. 2 in the sequence - by a user with profile ID=2',
                'comment_images' => array(
                    array('URL' => 'http://www.morning.dk/morning.hyperesources/morningnetwork-webV2.png'),
                    array('URL' => 'http://www.springfeed.com/images/logo_en.png')
                )
            )
        );

        return parent::getJsonForData($locationCommentData);
    }
}