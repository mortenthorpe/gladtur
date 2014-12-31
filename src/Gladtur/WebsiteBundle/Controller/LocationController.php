<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 8/6/13
 * Time: 10:12 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Gladtur\WebsiteBundle\Controller;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Geocoder\HttpAdapter\BuzzHttpAdapter;
use Gladtur\TagBundle\Entity\CommentMedia;
use Gladtur\TagBundle\Entity\Location;
use Gladtur\TagBundle\Entity\UserLocationComments;
use Gladtur\TagBundle\Entity\UserLocationData;
use Gladtur\TagBundle\Entity\UserLocationHours;
use Gladtur\TagBundle\Entity\UserLocationMedia;
use Gladtur\TagBundle\Entity\UserLocationTagData;
use Gladtur\TagBundle\Entity\UserLocationTagDataRepository as UltdRepository;
use Gladtur\TagBundle\Form\LocationTagType;
use Gladtur\TagBundle\Form\LocationType;
use Gladtur\TagBundle\Form\UserLocationDataType;
use Gladtur\TagBundle\Form\UserLocationTagDataType;
use Gladtur\WebsiteBundle\Form\LocationPublicType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AntiMattr\GoogleBundle\Maps\StaticMap;
//use AntiMattr\GoogleBundle\Maps\Marker; // Static google map, deprecated for IvoryGMap ! //
use Ivory\GoogleMap\Overlays\Animation;
use Ivory\GoogleMap\Overlays\Marker;
use Ivory\GoogleMap\Services\Geocoding\Geocoder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Gladtur\WebsiteBundle\Form\Type\CommentFormType;
use Symfony\Component\HttpFoundation\Session\Session;
use Solarium\QueryType\Select\Query\Query as Query;

class LocationController extends Controller{
    /**
     * @Route("sted/{locationslug}", name="location_details")
     * @Template("WebsiteBundle:Locations:details.html.twig")
     */
    public function detailAction($locationslug, $profileId=3){
        /**
         * @var Location $location
         */
        //$this->get('gladtur.stats');
        $location = $this->getDoctrine()->getManager()->getRepository('Gladtur\TagBundle\Entity\Location')->findOneBy(array('slug'=>$locationslug));
        if(!$location) return $this->render('TwigBundle:Exception:error404.html.twig');
        // Generate LIIP Imagine thumbnails if not present

        // Google maps //
        $map = $this->get('ivory_google_map.map');
        $map->setHtmlContainerId('map_canvas_location');
        if($location->getLatitude() && $location->getLongitude()){
            $map->setCenter($location->getLatitude(), $location->getLongitude());
            $marker = new Marker();

// Configure your marker options
            $marker->setPrefixJavascriptVariable('marker_');
            $marker->setPosition($location->getLatitude(), $location->getLongitude(), true);
            $marker->setAnimation(Animation::DROP);
            $marker->setOption('clickable', false);
            $marker->setOption('flat', true);
            $marker->setOptions(array(
                    'clickable' => false,
                    'flat'      => true,
                ));
            $map->addMarker($marker);
            $map->setMapOption('zoom',14);
        }
        else{
            // Rådhuspladsen GetCoords: 55.675283,12.570163
            $map->setCenter(55.675283,12.570163);
            $map->setMapOption('zoom',6);
        }

        $map->setLanguage('da');
        // ./ Google maps //
        // User comments //
        $location_comments = $this->getDoctrine()->getManager()->getRepository('GladturTagBundle:UserLocationComments')->findBy(array('location'=>$location), array('created'=>'DESC'));//$location->getUserComments();
        // ./ User comments //
        // Location User Tag data //
        if($this->getUser()){
            $profile = $this->getUser()->getProfile();
        }
        else{
            if($this->get('session')->get('pid')){
                $profileId = $this->get('session')->get('pid');
            }
            $profile = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\TvguserProfile', $profileId);
        }
        $locationId = $location->getId();
        $location = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\Location', $locationId);
   /*     $userLocTagData = $this->getDoctrine()->getManager()->getRepository('Gladtur\TagBundle\Entity\UserLocationTagData')->findBy(array('location'=>$location, 'user_profile'=>$profile), array('updated'=>'ASC'));
        //$userLocTags = array();
        foreach($userLocTagData as $tagdata){
            $userLocTags[$tagdata->getTag()->getId()] = $tagdata;
        }*/
        $profileIcon = ($profile->getPath())?$profile->getPath():'12_Inddividuelt-tilpasset-brugerprofil.png';
        // ./Location User tag Data //

        $tagsandValues = $this->_websitelocationTagsandValues($location->getId(), $profile->getId());
        $score = -1;
        if(count($tagsandValues)>0){
            $tags_sum = 0;
            foreach($tagsandValues as $tagId => $tagPropertiesAssoc){
                $tags_sum += intval($tagPropertiesAssoc['value']);
            }
            $score = $tags_sum / count($tagsandValues);
        }
        if(($score > 1) && ($score < 2)) {
            $score = 0;
        }
        if((0 < $score) && ($score < 1)) $score = 1;
        if($score == -1 ) $score = 3;
        $location->setScore($score);
        $scoreName = $this->getScoreName($score);

        return array(
            'location' => $location,
            'score' => $score,//$this->getDoctrine()->getRepository('GladturTagBundle:Location')->getScoreName($location, $profile),
            'scorename' => $scoreName,
            'profile' => $profile,
            'map' => $map,
            'comments_sorted' => $location_comments,
            //'user_loc_tags' => $userLocTags,
            'pagetitle' => ucfirst($location->getReadableName())
        );
    }

    /**
     * Finds and displays a Location entity.
     *
     * @Route("vis/sted/{locationslug}", name="location_public_show")
     * @Template()
     */
    public function showAction($locationslug)
    {
        $location = $this->getDoctrine()->getManager()->getRepository('GladturTagBundle:Location')->findOneBy(array('slug'=>$locationslug));

        if (!$location) {
            throw $this->createNotFoundException('Unable to find Location entity.');
        }

        $deleteForm = $this->createDeleteForm($location->getId());

        return array(
            'entity'      => $location,
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * @param $locationid
     * @Route("testjson/{locationslug}")
     */
    public function geocodeAddress($locationslug){
        $location = $this->getDoctrine()->getManager()->getRepository('GladturTagBundle:Location')->findOneBy(array('slug'=>$locationslug));
        $geocoder = new Geocoder();
        $adapter  = new BuzzHttpAdapter();
        $geocoder->registerProviders(array(
                new \Geocoder\Provider\GoogleMapsProvider(
                    $adapter, 'da', 'Denmark', false
                )));
// Geocode an address
        $response = $geocoder->geocode($location->getAddressStreet().', '.$location->getAddressZip().', '.$location->getAddressCountry());
        return new JsonResponse(array('lat'=>$response->getLatitude(), 'lon'=>$response->getLongitude()));
    }

    /**
     * @param Request $request
     * @Route("ultest")
     * @Template("WebsiteBundle:Default:ul.html.twig")
     */
    public function uploadTestAction(Request $request){
        return array();
    }

    /**
     * @Route("pluploadfile", name="_uploader_upload_subcategory_images")
     * @Secure({"ROLE_ADMIN", "ROLE_TVGUSER"})
     */
    public function uploadSecondaryImagesAction(Request $request){
        $location = $this->getDoctrine()->getManager()->getRepository('Gladtur\TagBundle\Entity\Location')->find($request->get('location_id'));
        if($location){
            $uldir = __DIR__ . '/../../../../web/uploads/locations/'.$request->get('location_id');
            $imgDelta=0;
            foreach ($request->files as $uploadedFile) {
                $imgDelta+=1;
                /**
                 * @var UploadedFile $uploadedFile
                 */
                $name = $uploadedFile->getClientOriginalName();
                $uploadedFile->move($uldir, $name);
                $locationMedia = new UserLocationMedia();
                $locationMedia->setMediaPath($name);
                $locationMedia->setLocation($location);
                $locationMedia->setUser($this->getUser());
                $locationMedia->setIsmainimage(false);
                $locationMedia->setDelta($imgDelta);
                $locationMedia->setPublished(true);
                $this->getDoctrine()->getManager()->persist($locationMedia);
                $this->getDoctrine()->getManager()->persist($location);
                $this->getDoctrine()->getManager()->flush();
            }
            return new JsonResponse(json_encode($request->files));
        }
        return new JsonResponse(array('success' => 0, 'msg_err' => 'The location you are trying to upload image data for does not exist!'));
    }

    /**
     * @Route("jqueryuploadfile", name="_uploader_upload_main_image")
     * @Secure({"ROLE_ADMIN", "ROLE_TVGUSER"})
     */
    public function uploadMainImageAction(Request $request){
        $location = $this->getDoctrine()->getManager()->getRepository('Gladtur\TagBundle\Entity\Location')->find($request->get('location_id'));
        if($location){
        $uldir = __DIR__ . '/../../../../web/uploads/locations/'.$request->get('location_id');
        /* Set the previous Main Images to unpublished  */
        $mainImagesPrev = $this->getDoctrine()->getManager()->getRepository('Gladtur\TagBundle\Entity\UserLocationMedia')->findBy(array('location' => $location, 'published' => true, 'ismainimage' => true));
          foreach($mainImagesPrev as $mainImage){
              $mainImage->setPublished(false);
              $this->getDoctrine()->getManager()->persist($mainImage);
          }
        foreach ($request->files as $uploadedFile) {
            /**
             * @var UploadedFile $uploadedFile
             */
            $name = $uploadedFile->getClientOriginalName();
            $uploadedFile->move($uldir, $name);
            $locationMedia = new UserLocationMedia();
            $locationMedia->setMediaPath($name);
            $locationMedia->setIsmainimage(true);
            $location->setMainImageThumbnail($name);
            $locationMedia->setLocation($location);
            $locationMedia->setUser($this->getUser());
            $locationMedia->setPublished(true);
            $this->getDoctrine()->getManager()->persist($locationMedia);
            $this->getDoctrine()->getManager()->persist($location);
            $this->getDoctrine()->getManager()->flush();
        }
        return new JsonResponse(json_encode($request->files));
        }
        return new JsonResponse(array('success' => 0, 'msg_err' => 'The location you are trying to upload image data for does not exist!'));
    }

    /**
     * @Route("opret", name="user_location_create")
     * @Secure({"ROLE_ADMIN", "ROLE_TVGUSER"})
     */
    public function createNewAction(){
        $location = new Location();
        $location->setPublished(false);
        $this->getDoctrine()->getManager()->persist($location);
        $this->getDoctrine()->getManager()->flush();
        $this->get('session')->set('newlocid', $location->getId());
        return $this->redirect('rediger/nytsted');
    }

    /**
     * @Route("rediger/{locationslug}", name="location_form_edit")
     * @Secure({"ROLE_ADMIN", "ROLE_TVGUSER"})
     * @Template("WebsiteBundle:Locations:edit.html.twig")
     */
    public function editLocationAction($locationslug){
        /*$this->get('session')->set('referrer_target_path', $this->generateUrl('location_details', array('locationslug'=>$locationslug,'action' => 'editAccLocation')));*/
        $newLocationId = null;
        if($locationslug == 'nytsted'){
            $newLocationId = $this->get('session')->get('newlocid', null);
            if($newLocationId){
              $location = $this->getDoctrine()->getManager()->getRepository('GladturTagBundle:Location')->find($newLocationId);
            }
        }
        else{
          $location = $this->getDoctrine()->getManager()->getRepository('GladturTagBundle:Location')->findOneBy(array('slug'=>$locationslug));
        }
        $locationUserData = $location->getUserLocationData(true);
        if(!$locationUserData){
            $locationUserData = new UserLocationData();
        }
        $location->setUserLocationDataLatest($locationUserData);
        $form = $this->createForm(new LocationPublicType(), $location);
        //$form->get('addressCityAndZip')->setData($location->getAddressCityAndZip());
        // Google maps //
        $map = $this->get('ivory_google_map.map');
        $map->setHtmlContainerId('map_canvas_edit');
        // Rådhuspladsen GetCoords: 55.675283,12.570163
        $map->setCenter(55.675283, 12.570163);
        $map->setLanguage('da');
        $map->setMapOption('zoom',6);
        return array(
            'form'   => $form->createView(),
            'location' => $location,
            'locationslug' => ($newLocationId)?$newLocationId:$location->getSlug(),
            'map' => $map,
            'pagetitle' => ($location->getReadableName())?'Rediger detaljer - ' . ucfirst($location->getReadableName()):'Opret nyt sted',
            'htmltitle' => true,
        );
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("sted/{locationslug}/redirect/editlocation", name="location_editlocation_redirectregister");
     */
    public function editLocationRegisterTargetPath($locationslug){
        $this->get('session')->set('referrer_target_path', $this->generateUrl('location_form_edit', array('locationslug'=>$locationslug)));
        return $this->redirect($this->generateUrl('fos_user_registration_register'));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("sted/{locationslug}/redirect/editlocationaccessibilites", name="location_editlocationaccessibilites_redirectregister");
     */
    public function editLocationAccessibilitiesRegisterTargetPath($locationslug){
        $this->get('session')->set('referrer_target_path', $this->generateUrl('location_details', array('locationslug'=>$locationslug,'action' => 'editAccLocation')));
        return $this->redirect($this->generateUrl('fos_user_registration_register'));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("sted/{locationslug}/redirect/commentlocation", name="location_commentlocation_redirectregister")
     */
    public function commentLocationRegisterTargetPath($locationslug){
        $this->get('session')->set('referrer_target_path', $this->generateUrl('location_details', array('locationslug'=>$locationslug,'action' => 'commentLocation')));
        return $this->redirect($this->generateUrl('fos_user_registration_register'));
    }

    /**
     * @Route("user_location_create_redirect_register", name="user_location_create_redirect_register")
     */
    public function createLocationRegisterTargetPath(){
        $this->get('session')->set('referrer_target_path', $this->generateUrl('user_location_create'));
        return $this->redirect($this->generateUrl('fos_user_registration_register'));
    }

    /**
     * @Route("rediger/sted/gem", name="location_form_submit")
     * @Secure({"ROLE_ADMIN", "ROLE_TVGUSER"})
     * @Method("POST")
     */
    public function saveLocationAction(Request $request){
        $newLocationId = $this->get('session')->get('newlocid', null);
        $slugOriginalTxt = '';
           if($newLocationId){
                $location = $this->getDoctrine()->getManager()->getRepository('GladturTagBundle:Location')->find($newLocationId);
                $location->setCreatedBy($this->getUser());
            }
        else{
            $locationslug = $this->getRequest()->get('locationslug');
            $location = $this->getDoctrine()->getManager()->getRepository('GladturTagBundle:Location')->findOneBy(array('slug'=>$locationslug));
            $slugOriginalTxt = $location->getSlug();
            }
        if($location){
            $postedData = $request->get('gladturlocation');
        /*if(!($location->getReadableName()) && !($location->getAddressStreet())){
            $postedData = $request->get('gladturlocation');
            $slugtxt = ($locationslug)?$locationslug:$postedData['readableName'].'-'.$postedData['addressStreet'];
            //$location->slugify($slugtxt);
        }
        else{
          $slugtxt = $location->getReadableName().'-'.$location->getAddressStreet();
          $location->slugify($slugtxt);
        }*/
        // Google maps //
        $map = $this->get('ivory_google_map.map');
        // Rådhuspladsen GetCoords: 55.675283,12.570163
        $map->setCenter(55.675283, 12.570163);
        $map->setLanguage('da');
        $map->setMapOption('zoom',6);
        $form = $this->createForm(new LocationPublicType(), $location);
        /*
         * $data = $request->request->all();
        $children = $form->all();
        $data = array_intersect_key($data, $children);
        */
        if($this->getUser()->hasRole('ROLE_ADMIN')){
                $admin_validated = (isset($postedData['admin_validated_boolean']) && (intval($postedData['admin_validated_boolean'])==1))?true:false;
                $location->setAdminValidatedBoolean($admin_validated);
        }
        else{
           unset($postedData['admin_validated_boolean']);
        }
        $form->bind($postedData); //$request will incorrectly include extra JS-generated fields by PLUpload file uploader
        if($form->isValid()){
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Dine ændringer er gemt!'
            );
            $em = $this->getDoctrine()->getManager();
          if($location->getAddressIsSet()){
              $geolocationApi = $this->get('google_geolocation.geolocation_api');
              $locationGeoPos = $geolocationApi->locateAddress($location->getFullAddress());
              $latLngAssoc = $locationGeoPos->getLatLng(0);
              $location->setLatitude($latLngAssoc['lat']);
              $location->setLongitude($latLngAssoc['lng']);
          }
            /**
             * @var UserLocationData $userLocationData
             */
            $userLocationDataReq = $request->get('gladturlocation');
            $userLocationDataReq = $userLocationDataReq['userLocationDataLatest'];
            // [daysHoursOpenClosed][0][timesTxt] in Form POSTed
            $userLocationData = new UserLocationData();
            if(isset($userLocationDataReq['daysHoursOpenClosed'])) {
                $userLocationHoursOpen = $userLocationDataReq['daysHoursOpenClosed'];

                foreach($userLocationHoursOpen as $idx => $timesTxt){
                    $timesTxt = (!is_array($timesTxt))?$timesTxt:implode(', ', $timesTxt);
                    $hoursOpen = new UserLocationHours();
                    $hoursOpen->setUserLocationData($userLocationData);
                    $userLocationData->addDaysHoursOpenClosed($hoursOpen);
                    $hoursOpen->setDayNumber($idx);
                    $hoursOpen->setTimesTxt($timesTxt);
                }
            }

            $userLocationData->setUser($this->getUser());
            $userLocationData->setProfile($this->getUser()->getProfile());
            $userLocationData->setLocation($location);
            $userLocationData->setMail($userLocationDataReq['mail']);
            $userLocationData->setPhone($userLocationDataReq['phone']);
            $userLocationData->setContactPerson($userLocationDataReq['contactPerson']);
            $userLocationData->setTxtDescription($userLocationDataReq['txtDescription']);
            $location->addUserLocationData($userLocationData);
            $location->setPublished(true);
            $editedSlugtxt = $this->slugify($location->getReadableName().'-'.$location->getAddressStreetAndExtd());
            if($slugOriginalTxt !== $editedSlugtxt){
            $similarSlugs = $this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\Location')->getLikelySlugs($this->slugify($editedSlugtxt));
            $slugCounter = 2;
            $newslugTxt = $editedSlugtxt;
            while(in_array($newslugTxt, $similarSlugs)){
                $newslugTxt = $editedSlugtxt . '-' . $slugCounter;
                $slugCounter++;
            }
            $location->slugify($newslugTxt);
            }
          $em->persist($location);
          $em->flush();
            if(false){ // Remove for live
            $solrClient = $this->container->get('solarium.client');
            $update = $solrClient->createUpdate();
            $update->addDocument($location->toSolariumDocument($solrClient));
            $update->addCommit();
            $solrClient->update($update);
            }
          $this->get('session')->remove('newlocid');
          }
            else{
                return $this->render('WebsiteBundle:Locations:edit.html.twig', array(
                    'form'   => $form->createView(),
                    'location' => $location,
                    'locationslug' => $location->getSlug(),
                    'map' => $map,
                    'pagetitle' => ($location->getReadableName())?'Rediger detaljer - ' . ucfirst($location->getReadableName()):'Opret nyt sted',
                    'htmltitle' => true,
                ));
          }
          //return $this->redirect($this->generateUrl('location_form_edit', array('id' => $id)));
        }

       /* return array(
            'form'   => $form->createView(),
            'location' => $location,
            'map' => $map,
            'pagetitle' => ($location->getReadableName())?'Rediger detaljer for ' . ucfirst($location->getReadableName()):'Opret nyt sted',
            'htmltitle' => true,
        );*/


      // $this->redirect($this->generateUrl('location_form_edit', array('id' => $location->getId())));
        return $this->redirect($this->generateUrl('location_details', array('locationslug' => $location->getSlug())));
    }

    /**
     * @Route("sted/{locationslug}/kommentar/tilfoej", name="location_add_comment")
     * @Template("WebsiteBundle:Locations:comment_add.html.twig")
     * @Secure({"ROLE_ADMIN", "ROLE_TVGUSER"})
     */
    public function locationAddCommentAction($locationslug){
        $location = $this->getDoctrine()->getManager()->getRepository('GladturTagBundle:Location')->find($locationslug);
        /*$this->get('session')->set('referrer_target_path', $this->generateUrl('location_details', array('locationslug'=>$locationslug,'action' => 'commentLocation')));*/
        $newUserLocationComment = new UserLocationComments();
        $newUserLocationComment->setLocation($location);
        $commentForm = $this->createForm(new CommentFormType(), $newUserLocationComment);
        return array(
            'locationslug' => $locationslug,
            'commentForm' => $commentForm->createView(),
        );
    }

    /**
     * @param Request $request
     * @Route("kommentar/gem", name="location_save_comment")
     * @Secure({"ROLE_ADMIN", "ROLE_TVGUSER"})
     */
    public function locationSaveCommentAction(Request $request){
        $reqArray = $request->get('location_comment');
        $locationslug = $request->get('locationslug');
        $location = $this->getDoctrine()->getRepository('GladturTagBundle:Location')->findOneBy(array('slug'=>$locationslug));
        $newComment = new UserLocationComments();
        $newComment->setLocation($location);
        $newComment->setUser($this->getUser());
        $newComment->setProfile($this->getUser()->getProfile());
        $commentForm = $this->createForm(new CommentFormType(), $newComment);
        /*$data = $request->request->get($commentForm->getName());
        $children = $commentForm->all();
        $data = array_intersect_key($data, $children);
        $commentForm->bind($data);*/
        $commentForm->bind($request);
        if($commentForm->isValid()){
          $this->getDoctrine()->getManager()->persist($newComment);
          $this->getDoctrine()->getManager()->flush();
          $imageFile = $commentForm['comment_image']->getData();
          if($imageFile){
            $locationUserCommentMedia = new CommentMedia();
              $maxdelta=$this->getDoctrine()->getManager()->getRepository('GladturTagBundle:CommentMedia')->createQueryBuilder('MaxDelta')->select('MAX(MaxDelta.delta)')->where('MaxDelta.comment='.$newComment->getId())->getQuery()->getSingleScalarResult();
              if($maxdelta){
                  $delta = $maxdelta;
              }
              else{
                  $delta=0;
              }
            $delta++;
            $locationUserCommentMedia->setDelta($delta);
            $locationUserCommentMedia->setComment($newComment);
            $imageBaseName = $locationUserCommentMedia->getComment()->getId().'_'.$delta;
            $imageFileExtension = ($imageFile->guessExtension())?$imageFile->guessExtension():'.png'; // @TODO: Insecure, we'd rather throw and exception here
            $imageFile->move($this->get('kernel')->getRootDir() . '/../web/uploads/'.$locationUserCommentMedia->getMyULDirPath(),$imageBaseName.'.'.$imageFileExtension);
            $locationUserCommentMedia->setMediaPath($imageBaseName.'.'.$imageFileExtension);
            $this->getDoctrine()->getManager()->persist($locationUserCommentMedia);
          }
          $this->getDoctrine()->getManager()->flush();
            $htmlSuccessResponse = 'Tak for din kommentar den er nu gemt<p>&nbsp;</p><p><a href="' . $this->generateUrl('location_details', array('locationslug' => $location->getSlug())) . '">Tilbage til stedet - \'' . $location->getReadableName() . '\'</a><br/><br/><a href="' . $this->generateUrl('location_details', array('locationslug' => $location->getSlug())) . '?action=commentLocation">Tilføj endnu en kommentar</a></p>';
            return new Response($htmlSuccessResponse);
          //return $this->redirect($this->generateUrl('location_details', array('locationslug' => $location->getSlug())));
        }
        else{
            return $this->render('WebsiteBundle:Locations:comment_add.html.twig', array(
                    'locationslug' => $locationslug,
                    'commentForm' => $commentForm->createView(),
                ));
        }

        return new Response('Fallback html out!');
    }

    /**
     * @Route("sted/tilgaengelighed/tilfoej", name="location_add_tagdata")
     * @Template("WebsiteBundle:Locations:_accessibilities_tags.html.twig")
     */
    public function locationAddTagDataAction(Request $request){
        $locationslug = $request->get('locationslug');
        if(!$request->get('pid', null) && $this->getUser()){
            $profileid = ($request->get('pid',null))?$request->get('pid'):$this->getUser()->getProfile()->getId();
            $profile = $this->getUser()->getProfile();
            if(!$profile->getIndividualized()){
                $profiles =  $this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\TvguserProfile')->findBy(array('individualized' => false), array('rank' => 'ASC'));
            }
            else{
                $profiles =  $this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\TvguserProfile')->findBy(array(), array('rank' => 'ASC'));
            }
        }
        else{
            $profileid = $request->get('pid', $this->get('session')->get('pid', null));
            if(!$profileid){
                $profile=$this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\TvguserProfile')->findOneBy(array('individualized' => false), array('rank' => 'ASC'));
                $profileid = $profile->getId();
            }
            $this->get('session')->set('pid', $profileid);
            $profiles = $this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\TvguserProfile')->findBy(array('individualized' => false), array('rank'=>'ASC'));
            $profile = $this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\TvguserProfile')->find($profileid);
        }
        $editAccessibilities = ($request->get('editacc', false))?true: false;
        $location = $this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\Location')->findOneBy(array('slug' => $locationslug));
        if($location && !$profile->getIndividualized()){
            $profileTagData = $profile->getTags($location->getTopCategory()->getId());
        }
        else{
            $profileTagData = $this->getUser()->getFreeprofile()->getProfileTags($location->getTopCategory()->getId());
        }

            $tagsandValues = array();
            //Get the tags and values from the Entity UserLocationTagData
            /*foreach($locationProfileTagData as $tagObj){
                $userLocationTagData = $this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\UserLocationTagData')->findBy(array('tag' => $tagObj, 'location' => $location, 'deletedAt' => null), array('id' => 'DESC'), 1);
                $userLocationTagData = array_shift($userLocationTagData);
                $tagvalue = ($userLocationTagData)?$userLocationTagData->getTagvalue():null;
                $tagsandValues[$tagObj->getId()] = $tagvalue;
            }*/
        $tagsandValues = $this->_websitelocationTagsandValues($location->getId(), $profile->getId());
        $score = -1;
        if(count($tagsandValues)>0){
        $tags_sum = 0;
        foreach($tagsandValues as $tagId => $tagPropertiesAssoc){
            $tags_sum += intval($tagPropertiesAssoc['value']);
        }
        $score = $tags_sum / count($tagsandValues);
        }
        if(($score > 1) && ($score < 2)) {
            $score = 0;
        }

        $scoreName = $this->getScoreName($score);
        return array(
          'locationslug' => $locationslug,
          'location_top_category_name' => $location->getTopCategory(),
          'profiles' => $profiles,
          'profile' => $profile,
          'profileTagData' => $profileTagData,
          'tagswithvalues' => $tagsandValues,
          'score' => $score,
          'scorename' => $scoreName,
          'pid' =>  $this->get('session')->get('pid'),
          'edit' => $editAccessibilities,
        );
    }


    /**
     * @Route("accessibilities_ajaxrender", name="accessibilities_ajaxrender")
     * @Method("GET")
     */
    public function ajaxTestRenderAction(Request $request){
        return $this->render('WebsiteBundle:Default:_ajax_profiles_radio_listitems.html.twig', $this->locationAddTagDataAction($request));
    }

    /**
     * @Route("sted/tilgaengelighed/gem", name="location_save_tagdata")
     * @Secure({"ROLE_ADMIN", "ROLE_TVGUSER"})
     */
    public function locationSaveTagDataAction(Request $request){
        $locationslug = $request->get('locationslug');
        $location = $this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\Location')->findOneBy(array('slug' => $locationslug));
        $tagsfromreq = $request->get('userlocationtags');
        if($request->get('pid', null)){
            $profileid = $request->get('pid');
            $profile = $this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\TvguserProfile')->find($profileid);
        }
        else{
            $profile = $this->getUser()->getProfile();
        };

        $em = $this->getDoctrine()->getManager();
        foreach($tagsfromreq as $tagId => $tagValue){
            $tagValue = intval($tagValue);
            $tag = $em->getRepository('GladturTagBundle:Tag')->find($tagId);
            $locationUserTag = new UserLocationTagData();
            $locationUserTag->setTagValue($tagValue);
            $locationUserTag->setUser($this->getUser());
            $locationUserTag->setLocation($location);
            $locationUserTag->setTag($tag);
            $locationUserTag->setUserProfile($profile);
            $em->persist($locationUserTag);
        }
        $em->flush();
        if(!$profile->getIndividualized()){
            $profileTagData = $profile->getTags($location->getTopCategory()->getId());
        }
        else{
            $profileTagData = $this->getUser()->getFreeprofile()->getProfileTags($location->getTopCategory()->getId());
        }

        $tagsandValues = $this->_websitelocationTagsandValues($location->getId(), $profile->getId());
        $score = -1;
        if(count($tagsandValues)>0){
            $tags_sum = 0;
            foreach($tagsandValues as $tagId => $tagPropertiesAssoc){
                $tags_sum += intval($tagPropertiesAssoc['value']);
            }
            $score = $tags_sum / count($tagsandValues);
        }
        if(($score > 1) && ($score < 2)) {
            $score = 0;
        }
        $scoreName = $this->getScoreName($score);

        $accessibilities_list_html = $this->renderView(
            'WebsiteBundle:Default:_ajax_profiles_radio_listitems.html.twig',
            array('profileTagData' => $profileTagData, 'edit' => false, 'tagswithvalues' => $this->_websitelocationTagsandValues($location->getId(), $profile->getId()), 'pid' =>  $this->get('session')->get('pid'))
        );
        return new JsonResponse(array('msg'=>'<div class="notice">Din anmeldelse er gemt!</div>', 'accessibilities_list_html' => $accessibilities_list_html, 'scorename' => $scoreName));
    }

    public function _websitelocationTagsandValues($locationid, $profileid){
        if(!$profileid) return array();
        $profileTagsRs = array();
        // Get the profile from a local selection first, and if not set then from the active user.
        $location = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\Location', $locationid);
        $profile = (is_numeric($profileid) && ($profileid > 2))?$this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\TvguserProfile')->find($profileid):$this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\TvguserProfile')->findOneBy(array('rank'=> 'ASC'));
        $profileqb = $this->getDoctrine()->getManager()->createQueryBuilder('profiletags');
        $tagvaluesqb = $this->getDoctrine()->getManager()->createQueryBuilder('tagvalues');
        if(!$profile) return array();
        if(!$profile->getIndividualized()){
            // Get all tags for a general non-individualized profile
            $profileTagsQb = $profileqb->select(array('profiletags.id id', 'profiletags.readableName', 'profiletags.textDescription', 'profiletags.iconPath icon'))->from('Gladtur\TagBundle\Entity\TvguserProfile', 'uprofile')->join('uprofile.tags', 'profiletags')->join('profiletags.location_categories', 'tag_locationcategories')/*->join('uprofile.userLocationTagData', 'loctagdata')*/->where('uprofile.id = '.$profileid)/*->andWhere('loctagdata.tagvalue IN (1,2)')*/;
            $profileTagsRs = $profileTagsQb->getQuery()->getArrayResult();
        }
        else{
            $profile = $this->getUser()->getFreeProfile();
            if($profile && $profile->getProfileActive()){
                $userTagsRs = $profile->getProfileTags();
                foreach($userTagsRs as $tag){
                    $profileTagsRs[] = array('id' => $tag->getId(), 'readableName' => $tag->getReadableName(), 'textDescription' => $tag->getTextDescription(), 'icon' => $tag->getIconPathRaw());
                }
            }
        }
        $profileTagIds = array();
        $profileTagAssoc = array();
        foreach($profileTagsRs as $profileTag){
            $profileTagIds[] = $profileTag['id'];
            $profileTagAssoc[$profileTag['id']] = array('name'=>$profileTag['readableName'], 'info' => $profileTag['textDescription'], 'icon'=>'/uploads/icons/tags/'.$profileTag['icon']);
        }
        $tagvaluesQb = $tagvaluesqb->select(array('identity(ultd.tag) tagid', 'ultd.tagvalue tagvalue'))->from('Gladtur\TagBundle\Entity\UserLocationTagData', 'ultd')->where('ultd.location = '.$locationid)->andWhere('ultd.tag IN (:tagids)')->andWhere('ultd.tagvalue IN (1,2)')->setParameter('tagids', $profileTagIds)->orderBy('ultd.created', 'ASC');
        $tagvalues = $tagvaluesQb->getQuery()->getArrayResult();
        $tagRs = array();
        foreach($tagvalues as $tagIdAndValueTuple){
            $tagRs[$tagIdAndValueTuple['tagid']] = array('id'=>$tagIdAndValueTuple['tagid'], 'value'=>$tagIdAndValueTuple['tagvalue'], 'name' => $profileTagAssoc[$tagIdAndValueTuple['tagid']]['name'], 'info' => $profileTagAssoc[$tagIdAndValueTuple['tagid']]['info'], 'icon' => $profileTagAssoc[$tagIdAndValueTuple['tagid']]['icon']);
        }
        return $tagRs;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("sted/{locationslug}/redirect/editacc", name="location_editacc_redirectregister");
     */
    public function editAccRegisterTargetPath($locationslug){
        $this->get('session')->set('referrer_target_path', $this->generateUrl('location_details', array('locationslug'=>$locationslug,'action' => 'editAccLocation')));
        return $this->redirect($this->generateUrl('fos_user_registration_register'));
    }

    /**
     * @Route("_ajax_zipcode_autocomplete", name="_ajax_zipcode_autocomplete")
     */
    function locationAjaxZipcodeAutocompleteAction(Request $request){
        $streetNameRaw = $request->get('streetname','');
        $streetnameUrlSafe = urlencode($streetNameRaw);
        $zipcodesJson = file_get_contents('http://webapi.aws.dk/vejnavne.json?vejnavn=' . $streetnameUrlSafe);
        $zipcodesAssoc = json_decode($zipcodesJson, true);
        $rsJsonAssoc = array();
        foreach($zipcodesAssoc as $zipcodeAssoc){
            $rsJsonAssoc[] = array('label'=> $zipcodeAssoc['navn'] . ', ' . $zipcodeAssoc['postnummer']['navn'],'streetname' => $zipcodeAssoc['navn'] , 'city' => $zipcodeAssoc['postnummer']['navn'], 'zipcode' => $zipcodeAssoc['postnummer']['nr']);
        }
        return new JsonResponse($rsJsonAssoc);
    }

    private function getDuplicateCheckForm(){
        $includeZipcode = false;
        $reqQuery = $this->getRequest()->get('form', null);
        $reqQueryTxt = ($reqQuery)?$reqQuery['location_name']:null;
        $defaultData = array('location_name'=>($reqQueryTxt)?$reqQueryTxt:'Stedets navn');
        if($includeZipcode){
        $citiesAndZip = file_get_contents('postnumre.json');
        $citiesAndZip = json_decode($citiesAndZip, true);
        $citiesAndZipList=array();
        foreach($citiesAndZip as $cityAndZipAssoc){
                $citiesAndZipList[$cityAndZipAssoc['nr']] = $cityAndZipAssoc['navn'] . '( ' . $cityAndZipAssoc['nr'] .' )';
        }
        $form = $this->createFormBuilder()->add('location_name', 'text', array('label'=>'Stedets navn', 'attr' => array('placeholder' => 'Stedets navn')))->add('city', 'choice', array('label'=>'I nærheden af, eller i byen','required'=>true, 'multiple'=>false,'expanded'=>false, 'attr'=>array('class'=>'cities_list'), 'choices' => $citiesAndZipList))->getForm();
        }
        else{
            $form = $this->createFormBuilder()->add('location_name', 'text', array('label'=>'Stedets navn', 'attr' => array('placeholder' => 'Stedets navn')))->getForm();
        }
        return $form;
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("_location_duplicatecheck", name="_location_duplicatecheck")
     */
    public function duplicateCheckAction(Request $request){
        $duplicateForm = $this->getDuplicateCheckForm();
        return $this->render(
            'WebsiteBundle:Locations:_duplicatecheck.html.twig',
              array(
                'form'   => $duplicateForm->createView()
              )
        );
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("_location_duplicatecheck_submit", name="_location_duplicatecheck_submit")
     */
    public function duplicateCheckSubmitAction(Request $request){
        $requestForm = $request->get('form');
        $location_readablename = isset($requestForm['location_name'])?$requestForm['location_name']:$request->get('location_name');
        $cityZipcode = isset($requestForm['city'])?$requestForm['city']:null;
        $zipcodePrecise = true;
        $matchCandidatesRs = array();
        if($location_readablename && $cityZipcode){
           if(!is_numeric($cityZipcode)){
             $zipcodePrecise = false;
             $cityZipsFromAndToArr = array();
             preg_match('/([0-9]+)\-?([0-9]+)/', $cityZipcode, $cityZipsFromAndToArr);
             $cityZipcodeFrom = $cityZipsFromAndToArr[1];
             $cityZipcodeTo = $cityZipsFromAndToArr[2];
           }
            $locationRepository = $this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\Location');
            $matchCandidatesQuery = $locationRepository->createQueryBuilder('l')->select('l.id, l.readableName, l.addressCity, l.addressZip')->where('l.published = true')->andWhere('l.readableName LIKE :location_readablename')->setParameter('location_readablename', '%' . $location_readablename . '%');
            if($zipcodePrecise){
                $matchCandidatesQuery = $matchCandidatesQuery->andWhere('l.addressZip = :cityZipcode')->setParameter('cityZipcode', $cityZipcode);
            }
            else{
                $matchCandidatesQuery = $matchCandidatesQuery->andWhere('l.addressZip >= :cityZipcodeFrom')->andWhere('l.addressZip <= :cityZipcodeTo')->setParameter('cityZipcodeFrom', $cityZipcodeFrom)->setParameter('cityZipcodeTo', $cityZipcodeTo);
                ;
            }
        }
        if($location_readablename && !$cityZipcode){
            $locationRepository = $this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\Location');
            $matchCandidatesQuery = $locationRepository->createQueryBuilder('l')->select('l.id, l.readableName, l.addressCity, l.addressZip')->where('l.published = true')->andWhere('l.readableName LIKE :location_readablename')->setParameter('location_readablename', '%' . $location_readablename . '%');
        }
        $matchCandidatesQuery->orderBy('l.readableName', 'ASC')->orderBy('l.addressZip', 'ASC');
        $matchCandidatesQuery = $matchCandidatesQuery->getQuery();
        $matchCandidatesRs = $matchCandidatesQuery->getResult();
        $matchCandidates = array();
        foreach($matchCandidatesRs as $locationCandidate){
            $matchCandidates[] = array('id'=> $locationCandidate['id'], 'label' => $locationCandidate['readableName'] . ' - ' . $locationCandidate['addressCity'] . '( ' . $locationCandidate['addressZip'] . ' )');
        }
        return new JsonResponse($matchCandidates);
    }

    /**
     * @param Request $request
     * @Route("_ajax_location_duplicates", name="_ajax_location_duplicates")
     */
    public function ajaxLocationDuplicatesAction(Request $request){
        $locationReadableName = $request->get('readableName', null);
        if(!$locationReadableName) return new Response('Not found!','404');
            $client = $this->container->get('solarium.client');
            $select = $client->createSelect();
            // Post-treat all search-strings for special characters and prepare for SOLR index format.
            $locationReadableName = str_replace(array('+', ' ', '&'),array('\+','', '\&'), $locationReadableName);
            $locationReadableName = str_replace(array('Æ','Ø','Å','æ','ø','å','\u00c6','\u00d8','\u00c5','\u00e6','\u00f8','\u00e5'), array('AE','OE','AA','ae','oe','aa','AE','OE','AA','ae','oe','aa'), $locationReadableName);
            $locationReadableName = strtolower($locationReadableName);
            $locationReadableName = trim($locationReadableName);
            // And pass the final query to SOLR object...
            $select->setQuery('name:'.$locationReadableName.'*');
            $lat = $this->get('session')->get('ulat', null);
            $lon = $this->get('session')->get('ulng', null);
            if($lat && $lon){
              // Sort by radial distance from reference lat,lon in SOLR, until a distance of $radius
              //Query::SORT_ASC
              $selectHelper = $select->getHelper();
              $radius = 1000; // Distance for radial search in Kilometers, covers Denmark, so OK with 1000.
              $select->createFilterQuery('distance')->setQuery($selectHelper->geofilt('location', $lat, $lon, $radius));
              $select->addSort('geodist(location,'. $lat .','.$lon.')', Query::SORT_ASC);
            }
        $results = $client->select($select);
        $duplicateCandidates = array();
        foreach($results as $document){
            foreach($document as $field => $value){
                if(is_array($value)) continue;
                if($field == 'id'){
                    //$locationTmp = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\Location', intval($value));
                    $duplicateCandidates[]=intval($value);
                }
            }
        }

        $locations = array();
        foreach($duplicateCandidates as $locationId){
            $location = $this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\Location')->find($locationId);
            $locations[] = array('label'=>$location->getReadableName(), 'slug'=>$location->getSlug(), 'readableName' => $location->getReadableName(), 'streetname' => $location->getAddressStreet(), 'city' => $location->getAddressCity());
        }
        if(!empty($locations)){
            return new JsonResponse($locations);
        }
        else return new JsonResponse('');
        }

    /**
     * @Route("testslug/{slugtxt}")
     */
    public function testSlugifyAction($slugtxt){
        $similarSlugs = $this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\Location')->getLikelySlugs($slugtxt);
        $slugCounter = 2;
        $newslugTxt = $slugtxt;
        while(in_array($newslugTxt, $similarSlugs)){
            $newslugTxt = $slugtxt . '-' . $slugCounter;
            $slugCounter++;
        }
        return new Response($newslugTxt);
    }

    public function slugify($srcString){
        $srcString = str_replace(array('&#34;','&#38;','&#39;','æ','ø','å','Æ','Ø','Å',' ','&','.',',', '--', '---', '_-','!','--', 'a/s', '/', '\\', '\'','%','é','(',')','[',']','©','ã', 'ü', 'Ü','ä','Ä','–','@','è','ö','Ö','´','ô','Ô','ê',':',';','+','á','À','?','`','°','ó','–'), array('','','','ae','oe','aa','ae','oe','aa','-','-og-','_','_','-','-','_','','-','-as', '_', '_','','pct','e','','','','','c','a','y','Y','ae','Ae','-','at','e','oe','Oe','','o','O','e','-','','-','a','A','','','grader','o','-'), $srcString);
        $srcString = urlencode(substr(mb_strtolower($srcString), 0, 254));
        $srcString = preg_replace('/\%\w\w/i','', $srcString);
        return $srcString;
    }

    function getScoreName($scoreval = 0){
        if($scoreval == 0) return 'neutral';
        if($scoreval == 1) return 'down';
        if($scoreval == 2) return 'up';
        return 'unrated';
    }

    /**
     * @Route("_location_ajax_score", name="location_ajax_score")
     */
    public function _ajaxScore(Request $request){
        $locationslug = $request->get('locationslug', null);
        $location = $this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\Location')->findOneBy(array('slug' => $locationslug));
        if(!$location) return new JsonResponse(array('scorename' => ''));
        $profileId = $request->get('pid', 3);
        $tagsandValues = $this->_websitelocationTagsandValues($location->getId(), $profileId);
        $score = -1;
        if(count($tagsandValues)>0){
            $tags_sum = 0;
            foreach($tagsandValues as $tagId => $tagPropertiesAssoc){
                $tags_sum += intval($tagPropertiesAssoc['value']);
            }
            $score = $tags_sum / count($tagsandValues);
        }
        if(($score > 1) && ($score < 2)) {
            $score = 0;
        }
        $scoreName = $this->getScoreName($score);
        return new JsonResponse(
            array('scorename' => $scoreName)
        );
    }
}
