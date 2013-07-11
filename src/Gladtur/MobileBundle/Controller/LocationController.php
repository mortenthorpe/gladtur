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
use Gladtur\TagBundle\Entity\Location;
use Gladtur\TagBundle\Entity\Tag;
use Gladtur\TagBundle\Entity\UserLocationTagData;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use \Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\Request;

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
                'address' => array('zip' => '1705 KBH K', 'streetname' => 'Gl. Kongevej 56, stuen'),
                'score' => 1,
                'lat' => 0,
                'lon' => 0,
                'distance' => -1,
                'thumbnail' => 'http://images.apple.com/home/images/ios_title.png'
            ),
            array(
                'id' => 2,
                'topcatid' => 10,
                'name' => 'Sushitarian',
                'address' => array('zip' => '1123 KBH K', 'streetname' => 'Gothersgade 3'),
                'score' => 4,
                'lat' => 55.963901,
                'lon' => 12.281552,
                'distance' => 235,
                'thumbnail' => 'http://morning.dk/morning.hyperesources/morningnetwork-webV2.png'
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
                'address' => array('zip' => '1705 KBH K', 'streetname' => 'Gl. Kongevej 56, stuen'),
                'score' => 1,
                'lat' => 55.85,
                'lon' => 12.275,
                'distance' => 690,
                'thumbnail' => 'http://images.apple.com/home/images/ios_title.png'
            ),
            array(
                'id' => 2,
                'topcatid' => 10,
                'name' => 'Sushitarian',
                'address' => array('zip' => '1123 KBH K', 'streetname' => 'Gothersgade 3'),
                'score' => 4,
                'lat' => 55.963901,
                'lon' => 12.281552,
                'distance' => 235,
                'thumbnail' => 'http://morning.dk/morning.hyperesources/morningnetwork-webV2.png'
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
            'score' => 1,
            'address' => array('zip' => '1705 KBH K', 'streetname' => 'Gl. Kongevej 56, stuen'),
            'opening_hours' => array('open' => '09:00', 'close' => '18:00'),
            'phone' => '+45 12 34 56 78',
            'email' => 'claus@meyers.dk',
            'website' => 'http://meyers.dk',
            'description' => 'Meyers Madhus. Spis her, eller tag med hjem. Dagligt gadekøkken med hovedretter der mætter de fleste fra Kr. 69 til 135',
            'contact_person' => 'Claus Meyer',
            'properties' => array(
                array(
                    'id' => 1,
                    'name' => 'Niveaufri adgang',
                    'info' => 'Niveaufri adgang betyder at der slet ikke er trapper, eller at trapperne kan omgåes ved rampe til f.eks. rullestol',
                    'value' => 1
                )
            ),
            'comments' => array(
               array('profileid' => 1, 'username' => 'mortenuser_1976_dk', 'created_at' => time(), 'comment_txt' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat', 'comment_thumbnails' => array(
                   array('delta' => 0, 'URL' => 'http://images.apple.com/home/images/ios_title.png'),
                   array('delta' => 1, 'URL' => 'http://morning.dk/morning.hyperesources/morningnetwork-webV2.png')
               ),),
                array('profileid' => 2, 'username' => 'wheelchairthomas', 'created_at' => time()-100000, 'comment_txt' => 'Thomas\' comment - Right now just Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat', 'comment_thumbnails' => array(),)
            ),
        );

        return parent::getJsonForData($locationDetailData);
    }

    /**
     * @Route("/location/locationid/{locationid}/comments")
     * @TODO: REMOVE function - Marked for deprecation !
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

    /**
     * @Route("location/edit", name="create_edit_location")
     * @param Request $request
     */
    public function createLocationAction(Request $request){
        $locationid=$request->get('locationid', null);
        if($locationid){
         $location = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\Location',$request->get('locationid'));
         $op = 'edited';
        }
        else{
        $location = new Location();
        $location->setPublished(true);
        $location->setReadableName($request->get('name', 'Untitled'));
        $topCatId = $request->get('topcategory', 1);
        $tmpTopCategory = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\LocationCategory',$topCatId);
        $location->setTopCategory($tmpTopCategory);
        $op = 'created';
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($location);
        $em->flush();
        $tmpTopCategory = null;
        return parent::getJsonForData(array('success'=>1, 'locationid'=>$location->getId(), 'op' => $op));
    }

    /**
     * @Route("test/loctags")
     */
    public function locationTags(Request $request){
        $locationid = $request->get('locationid', null);
        $location_tags = array();
        $location=null;
        if($locationid){
            $location = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\Location', $locationid);
            if($location){
            $locationMappedTags = $location->getUserLocationTagData();
            if(!empty($locationMappedTags)){
            /**
             * @var UserLocationTagData $location_tag_data
             */
            foreach($locationMappedTags as $location_tag_data){
                /**
                 * @var Tag $tag
                 */
                $tag = $location_tag_data->getTag();
                if($tag->getPublished()){
                    $location_tags[] = array('id'=>$tag->getId(), 'name'=>$tag->getReadableName(), 'info'=>$tag->getTextDescription());
                }
            }
            }
            }
        }
      if(!$location || empty($location_tags)){
          /** @TODO Remove/refactor duplicate code below of action in MobileBundle\TagController */
          $em = $this->getDoctrine()->getManager();
          $tagEntities = $em->getRepository('GladturTagBundle:Tag')->findAll();
          $tags = array();
          foreach($tagEntities as $tag){
              if($tag->getPublished()){
                  $tags[] = array('id'=>$tag->getId(), 'name' => $tag->getReadableName(), 'info' => $tag->getTextDescription());
              }
          }
          $location_tags = $tags;
        }
        return parent::getJsonForData($location_tags);
    }
}