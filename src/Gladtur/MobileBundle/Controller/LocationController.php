<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 6/12/13
 * Time: 8:32 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Gladtur\MobileBundle\Controller;

use Doctrine\ORM\EntityManager;
use Gladtur\TagBundle\Controller\JsonController;
use Gladtur\TagBundle\Entity\CommentMedia;
use Gladtur\TagBundle\Entity\Location;
use Gladtur\TagBundle\Entity\LocationCategory;
use Gladtur\TagBundle\Entity\Tag;
use Gladtur\TagBundle\Entity\UserLocationComments;
use Gladtur\TagBundle\Entity\UserLocationData;
use Gladtur\TagBundle\Entity\UserLocationHours;
use Gladtur\TagBundle\Entity\UserLocationMedia;
use Gladtur\TagBundle\Entity\UserLocationTagData;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use \Doctrine\Common\Collections\Criteria;
use Solarium\Core\Client\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class LocationController extends JsonController
{
    /**
     * @Route("locations/profileid/{profileid}/catid/{catid}/lat/{lat}/lon/{lon}/p/{page}", defaults={"lat" = 0, "lon" = 0, "catid" = 0})
     * @Route("locations/profileid/{profileid}/catid/{catid}/lat/{lat}/lon/{lon}/p/{page}/token/{token}", defaults={"lat" = 0, "lon" = 0, "catid" = 0})
     * @Method("GET")
     */
    public function indexAction($profileid=0, $catid=0, $lat=0, $lon=0, $page=0, $token=0){
        $userid = null;
        $profile = null;
        $user = null;
        /**
         * @var Location $location
         */
        if($token !== 0){
            $um = $this->container->get('fos_user.user_manager');
            $user = $um->findUserBy(array('salt'=>$token));
            $userid = $user->getId();
            $profile = $user->getProfile()->getId();
        }

        if($profileid>=3){
            $profile = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\TvguserProfile', $profileid);
        }
        else{
            $profile = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\TvguserProfile', 3);
        }
        $allLocationsCount = $this->getDoctrine()->getRepository('GladturTagBundle:Location')->getAllLocationsCount(($catid==0)?null:$catid);

       // $locationsData = $locationsHelper->getPagedLocations($allLocations, $page, 10, $lat, $lon, $profile);
        $locationsData = $this->getDoctrine()->getRepository('GladturTagBundle:Location')->getPlacesOrderByDistance($user, $profile, $lat, $lon, ($catid==0)?null:$catid, $page, 10);

        if(empty($locationsData)){
            return parent::getJsonForData(array('success'=>0, 'totalcount'=>0));
        }
        $locationsData = array_merge(array('places'=>$locationsData), array('totalcount'=>$allLocationsCount));
        file_put_contents('/symftemp/at_'.date('d-m-h_i_s').'__useragent.txt', $this->getRequest()->headers->get('User-Agent'));
        return parent::getJsonForData($locationsData);
    }

    /**
     * @Route("locations_profile/profileid/{profileid}/catid/{catid}/lat/{lat}/lon/{lon}/p/{page}", defaults={"lat" = 0, "lon" = 0, "page" = 1})
     * @Route("locations_profile/profileid/{profileid}/catid/{catid}/lat/{lat}/lon/{lon}/p/{page}/token/{token}", defaults={"lat" = 0, "lon" = 0, "page" = 1})
     * @Method("GET")
     */
    public function nearbyplacesAction($profileid=null, $catid=null, $lat=0, $lon=0, $page=0, $token = 0)
    {
        return $this->indexAction($profileid, 0, $lat, $lon, $page, $token);
    }

    /**
     * @Route("location/locationid/{locationid}/profileid/{profileid}/lat/{lat}/lon/{lon}", defaults={"lat" = 0, "lon" = 0})
     * @Route("location/locationid/{locationid}/profileid/{profileid}/lat/{lat}/lon/{lon}/token/{token}", defaults={"lat" = 0, "lon" = 0})
     * @Method("GET")
     */
    public function placeDetailAction($locationid, $profileid, $lat=0, $lon=0, $token=0)
    {
        $userid = null;
        /**
         * @var Location $location
         */
        if($token !== 0){
            $um = $this->container->get('fos_user.user_manager');
            $user = $um->findUserBy(array('salt'=>$token));
            $userid = $user->getId();
        }
        $location = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\Location',$locationid);
        if(!$location->getPublished()) return $this->redirect('locations/profileid/'.$profileid.'/catid/0/lat/0/lon/0/p/0');
        $avalancheService = $this->get('liip_imagine.cache.manager');
        $locationsHelper = $this->get('gladtur.location.pagedlocations');
        $thumbnailImagePath = '';
        if($location->getMainImageThumbnail()){
            $mainThumbnailImagePath = $avalancheService->getBrowserPath('locations/'.$location->getId().'/'.$location->getMainImageThumbnail(), 'thumbnail', true);
            $mainImagePathFullsize = $avalancheService->getBrowserPath('locations/'.$location->getId().'/'.$location->getMainImageThumbnail(), 'fullsize', true);
        }
        else{
            $mainThumbnailImagePath = $avalancheService->getBrowserPath('noimage.png', 'thumbnail', true);
            $mainImagePathFullsize = $avalancheService->getBrowserPath('noimage.png', 'fullsize', true);
        }

        $subcategoriesAssoc = array();
        $place_imagesAssoc = array();
        $propertiesAssoc = array();
        $locationUserDataRs = array();
        if($location){
            $locationSubcategories = $location->getLocationCategories();
            /**
             * @var LocationCategory $lsubcategory
             */
            foreach($locationSubcategories as $lsubcategory){
                $subcategoriesAssoc[] = array('id'=>$lsubcategory->getId(), 'name' => $lsubcategory->getReadableName());
            }
            $locationSubMedia = $location->getMedia()->toArray();
            /**
             * @var UserLocationMedia $media
             */
            foreach($locationSubMedia as $media){
                if($media->getMediaPath() && !$media->getIsmainimage()){
                    $thumbnailImagePath = $avalancheService->getBrowserPath('locations/'.$location->getId().'/'.$media->getMediaPath(), 'thumbnail', true);
                    $fullsizeImagePath = $avalancheService->getBrowserPath('locations/'.$location->getId().'/'.$media->getMediaPath(), 'fullsize', true);
                }
                else{
                    $thumbnailImagePath = $avalancheService->getBrowserPath('noimage.png', 'thumbnail', true);
                    $fullsizeImagePath = $avalancheService->getBrowserPath('noimage.png', 'fullsize', true);
                }
                $place_imagesAssoc[] = array('delta'=>$media->getDelta(), 'URL' => $thumbnailImagePath, 'URL_full' => $fullsizeImagePath);
            }

            if($profileid && ($profileid>0)){
                $profile = $this->getDoctrine()->getManager()->find('GladTur\TagBundle\Entity\TvguserProfile', $profileid);
                // Get User-specific data, according to our selection rules //
                /**
                 * @var UserLocationData $locationUserData
                 */
                $locationUserData = $location->getUserLocationData(true);
                //$locationUserData = $this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\UserLocationData')->findOneBy(array('location'=>$location));
                $locationUserDataRs = array();
                if($locationUserData){
                    $openingHoursAssoc = array();
                    foreach($locationUserData->getDaysHoursOpenClosed() as $openHours){
                        $openingHoursAssoc[] = array('day'=>$openHours->getDayNumber(), 'hours' => $openHours->getTimesTxt());
                    }
                    $locationUserDataRs = array(
                    'phone' => array('value'=>($locationUserData->getPhone())?$locationUserData->getPhone():'', 'isnull'=>($locationUserData->getPhone())?0:1),
                'email' => array('value'=>($locationUserData->getMail())?$locationUserData->getMail():'','isnull'=>($locationUserData->getMail())?0:1),
                'description' => array('value'=>($locationUserData->getTxtDescription())?$locationUserData->getTxtDescription():'', 'isnull'=>($locationUserData->getTxtDescription())?0:1),
                'contact_person' => array('value'=>($locationUserData->getContactPerson())?$locationUserData->getContactPerson():'', 'isnull'=>($locationUserData->getContactPerson())?0:1),
                 'hours' => $openingHoursAssoc,
                    );
                }
                else{
                    $locationUserDataRs = array(
                        'phone' => array('value'=>'', 'isnull'=>1),
                        'email' => array('value'=>'', 'isnull'=>1),
                        'description' => array('value'=>'', 'isnull'=>1),
                        'contact_person' => array('value'=>'', 'isnull'=>1),
                        'hours' => array(),
                    );
                }
                /**
                 * @var UserLocationTagData $tagData
                 */
                $locationUserTagData = array();
                /** End of OLD Method */
                /** OLD Method, Replaced by */
                //$propertiesAssoc = $this->getDoctrine()->getRepository('GladturTagBundle:Location')->getTagsValues($location);
                $propertiesAssoc = $this->_mobilelocationTagsandValues($locationid, $profileid, $userid);
            }

            $commentsRaw = $location->getUserComments(); /**  comment_thumbnails are the plain file-name, need to be processed by image-handler (Avalanche in this case) */
            $comments = array();
            /**
             * @var UserLocationComments $comment
             */

            foreach($commentsRaw as $comment){
                /**
                 * @var CommentMedia $cMedia
                 */
                $commentMediaAssoc = array();
                foreach($comment->getMedia() as $cMedia){
                    //$thumbImgURL = $avalancheService->getBrowserPath($filepathBase.'/'.$comment->getId().'_'.$cMedia->getMediaPath(), 'thumbnail', true);
                    $commentMediaAssoc[] = array('delta'=>$cMedia->getDelta(), 'URL'=>$avalancheService->getBrowserPath('comments/'.$comment->getId().'_'.$cMedia->getDelta().'.jpg', 'thumbnail', true), 'URL_full' => $avalancheService->getBrowserPath('comments/'.$comment->getId().'_'.$cMedia->getDelta().'.jpg', 'fullsize', true));
                }
                $comments[$comment->getId()] = array('id'=>$comment->getId(), 'profileid'=>$comment->getProfile()->getId(), 'created_at'=>$comment->getCreated(), 'username'=>$comment->getUser()->getUsername(),'comment_thumbnails'=>(count($commentMediaAssoc))?$commentMediaAssoc:null,'comment_txt'=>$comment->getCommentTxt(), 'icon' => 'http://gladtur.dk'.'/uploads/avalanche/thumbnail/icons/profiles/'.$comment->getProfile()->getPath());
            }
            if(isset($comments)){
            //    asort($comments);
                $comments=array_values($comments);
            }
            $locationDistance=-1;
            if(($lat > 0) && ($lon > 0)){
                $locationDistance = abs($locationsHelper->haversineGreatCircleDistance($lat, $lon, $location->getLatitude(), $location->getLongitude()));
            }

            $tagsandValues = $this->_mobileLocationTagsandValues($location->getId(), $profileid, $userid);
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
            if($score == -1 ) $score = 3;
            $locationDetailData = array(
                'name' => $location->getReadableName(),
                'lat' => $location->getLatitude(),
                'lon' => $location->getLongitude(),
                'distance' => $locationDistance,
                'topcat' => array('id'=>$location->getTopCategory()->getId(), 'name'=>$location->getTopCategory()->getReadableName()),
                'subcategories' => $subcategoriesAssoc,
                'image_primary' => $mainThumbnailImagePath,//$this->getRequest()->getUriForPath('/uploads/avalanche/fullsize/locations/'.$location->getId().'/'.$location->getMainImage()->getMediaPath()),
                'image_primary_full' => $mainImagePathFullsize,
                'score' => $score,//$this->getDoctrine()->getRepository('GladturTagBundle:Location')->getScoreval($location, $profile),
                'address' => $location->getAddressAssoc(),
                'website' => array('value'=>($location->getHomepageReadable())?$location->getHomepageReadable():'', 'isnull'=>($location->getHomepageReadable())?0:1),
                'place_images' => $place_imagesAssoc,
                'comments' => (count($comments))?$comments:null,
                'profile_name' => $profile->getReadableName(),
                'icon' => 'http://gladtur.dk'.'/uploads/avalanche/thumbnail/icons/profiles/'.$profile->getPath(),
            );
            $locationDetailData = array_merge($locationDetailData, $locationUserDataRs);
            $locationValidated = (!$location->getAddressValidated() && $location->getOsmId())?0:1;
            $locationStreetNameExtd = '';
            if($locationValidated == 0){
                $locationDetailData['address']['streetname_extd'] = $location->getAddressExtd();
            }
            else{
                $locationDetailData['address']['streetname_extd'] = '';
            }
            $locationDetailData = array_merge($locationDetailData,array('properties' => $propertiesAssoc), array('validated'=>$locationValidated));
        }
        if(!$location){
        $locationDetailData = array();
        }

        return parent::getJsonForData($locationDetailData);
    }

    public function _mobileLocationTagsandValues($locationid, $profileid, $userid = null){
        $profileTagsRs = array();
        $location = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\Location', $locationid);
        $profile = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\TvguserProfile', $profileid);
        $profileqb = $this->getDoctrine()->getManager()->createQueryBuilder('profiletags');
        $tagvaluesqb = $this->getDoctrine()->getManager()->createQueryBuilder('tagvalues');
        if(!$profile->getIndividualized()){
            // Get all tags for a general non-individualized profile
            /*$profileTagsQb = $profileqb->select(array('profiletags.id id', 'profiletags.readableName', 'profiletags.textDescription', 'profiletags.iconPath icon'))->from('Gladtur\TagBundle\Entity\TvguserProfile', 'uprofile')->join('uprofile.tags', 'profiletags')->join()->where('uprofile.id = '.$profileid);
            $profileTagsRs = $profileTagsQb->getQuery()->getArrayResult();*/
            $userTagsRs = $profile->getTags($location->getTopCategory()->getId());
        }
        elseif($userid){
            $um = $this->container->get('fos_user.user_manager');
            $user = $um->findUserBy(array('id'=>$userid));
            $profile = $user->getFreeProfile();
            if($profile && $profile->getProfileActive()){
                $userTagsRs = $profile->getProfileTags($location->getTopCategory()->getId());
            }
        }
        foreach($userTagsRs as $tag){
            $profileTagsRs[] = array('id' => $tag->getId(), 'readableName' => $tag->getReadableName(), 'textDescription' => $tag->getTextDescription(), 'icon' => $tag->getIconPathRaw());
        }
        $profileTagIds = array();
        $profileTagAssoc = array();
        foreach($profileTagsRs as $profileTag){
            $profileTagIds[] = $profileTag['id'];
            $profileTagAssoc[$profileTag['id']] = array('name'=>$profileTag['readableName'], 'info' => $profileTag['textDescription'], 'icon'=>'http://gladtur.dk/uploads/icons/tags/'.$profileTag['icon']);
        }
        $tagvaluesQb = $tagvaluesqb->select(array('identity(ultd.tag) tagid', 'ultd.tagvalue tagvalue'))->from('Gladtur\TagBundle\Entity\UserLocationTagData', 'ultd')->where('ultd.location = '.$locationid)->andWhere('ultd.tag IN (:tagids)')->andWhere('ultd.tagvalue IN (1,2)')->setParameter('tagids', $profileTagIds)->orderBy('ultd.created', 'ASC');
        $tagvalues = $tagvaluesQb->getQuery()->getArrayResult();
        $tagRs = array();
        foreach($tagvalues as $tagIdAndValueTuple){
            $tagRs[$tagIdAndValueTuple['tagid']] = array('id'=>$tagIdAndValueTuple['tagid'], 'value'=>$tagIdAndValueTuple['tagvalue'], 'name' => $profileTagAssoc[$tagIdAndValueTuple['tagid']]['name'], 'info' => $profileTagAssoc[$tagIdAndValueTuple['tagid']]['info'], 'icon' => $profileTagAssoc[$tagIdAndValueTuple['tagid']]['icon']);
        }
        return array_values($tagRs);
    }

    /**
     * @Route("location/edit", name="create_edit_location")
     * @param Request $request
     * http://wiki.solarium-project.org/index.php/V3:Read-Write_document -- Solr WIKI for index manipulation
     * @Method("POST")
     */
    public function createLocationAction(Request $request){
        $name = null;
        $topCatId = null;
        $location = null;
        $location_id=null;
        $latitude=null;
        $longitude=null;
        $address_zip=null;
        $address_streetname=null;
        $op=null;
        $success=0;
        $solrClient = $this->container->get('solarium.client');
        if(parent::getIsJSON()){
            if(parent::getTokenPassed($request)){
               $em=$this->getDoctrine()->getManager();
            $requestContent = parent::getRequestFromJSON($request);
            if(isset($requestContent['locationid'])){
                // Editing a place via JSON-request //
                $location = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\Location',$requestContent['locationid']);
                $op = 'editedjson';
                /**
                 * @var Client $solrClient
                 */
                $update = $solrClient->createUpdate();
                $update->addDeleteQuery('id:'.$location->getId());
                $update->addCommit();
                $rs = $solrClient->update($update);
            }
            else{
                // Creating a place via JSON-request //
                $location = new Location();
                $location->setCreatedBy(parent::getUser());
                $topCatId = isset($requestContent['topcat'])?$requestContent['topcat']:null;
                if(!$topCatId){
                    return parent::getJsonForData(array('success'=>0, 'msg'=>'Du skal vælge en top-kategori for at oprette et sted'));
                }
                $tmpTopCategory = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\LocationCategory',$topCatId);
                if(!$tmpTopCategory){
                    return parent::getJsonForData(array('success'=>0, 'msg'=>'Du skal vælge en eksisterende top-kategori for at oprette et sted'));
                }

                $name = isset($requestContent['name'])?$requestContent['name']:'Untitled';
                $location->setLatitude(isset($requestContent['lat'])?$requestContent['lat']:null);
                $location->setLongitude(isset($requestContent['lon'])?$requestContent['lon']:null);
                $location->setAddressZip(isset($requestContent['address']['zip'])?$requestContent['address']['zip']:null);
                $location->setAddressStreet(isset($requestContent['address']['streetname'])?$requestContent['address']['streetname']:null);
                $location->setAddressCity(isset($requestContent['address']['city'])?$requestContent['address']['city']:'København');
                $location->setAddressCountry(isset($requestContent['address']['country'])?$requestContent['address']['country']:'Danmark');
                $location->setAddressExtd(isset($requestContent['address']['address_extd'])?$requestContent['address']['address_extd']:null);
                $location->setAddressValidated(isset($requestContent['address_validated'])?$requestContent['address_validated']:0);
                    if(parent::getUser()->hasRole('ROLE_OSM')){
                        if(!isset($requestContent['osm_id'])) exit;
                        $locationWithOSMId = $this->getDoctrine()->getRepository('GladturTagBundle:Location')->findBy(array('osm_id'=>$requestContent['osm_id'], 'published'=>true));
                        if($locationWithOSMId) return parent::getJsonForData(array('success'=>0, 'msg'=>'Duplicate OSM ID'));
                        $location->setOsmId($requestContent['osm_id']);
                        }
                $op = 'createdjson';
            }
                if(isset($requestContent['lat'])){
                    $location->setLatitude($requestContent['lat']);
                }
                if(isset($requestContent['lon'])){
                    $location->setLongitude($requestContent['lon']);
                }
                if(isset($requestContent['address']['streetname'])){
                    $location->setAddressValidated(true);
                    $location->setAddressStreet($requestContent['address']['streetname']);
                }
                if(isset($requestContent['address']['zip'])){
                    $location->setAddressValidated(true);
                    $location->setAddressZip($requestContent['address']['zip']);
                }
                if(isset($requestContent['address']['city'])){
                    $location->setAddressValidated(true);
                    $location->setAddressCity($requestContent['address']['city']);
                }
                
                $location->setHomepage(isset($requestContent['website'])?$requestContent['website']:null);
                if(isset($requestContent['subcategories'])){
                foreach($requestContent['subcategories'] as $subcategoryid){
                    $subcategory=$this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\LocationCategory', $subcategoryid);
                    $location->addLocationCategories($subcategory);
                }
                }
                $em->persist($location);
                $em->flush();

                /** Save the user-specific data for the location */
                $userLocationData = new UserLocationData();
                $userLocationData->setUser(parent::getUser());
                $userLocationData->setProfile(parent::getUser()->getProfile());
                $userLocationData->setContactPerson(isset($requestContent['contact_person'])?$requestContent['contact_person']:null);
                $userLocationData->setPhone(isset($requestContent['phone'])?$requestContent['phone']:null);
                $userLocationData->setMail(isset($requestContent['email'])?$requestContent['email']:null);

                $userLocationData->setLocation($location);
                $userLocationData->setTxtDescription(isset($requestContent['description'])?$requestContent['description']:null);

                $em->persist($userLocationData);

                /** Save the opening hours for days 0-6 of the week (0=Monday) */
                if(isset($requestContent['hours'])){
                foreach($requestContent['hours'] as $openingDayHour){
                    /**
                     * @var UserLocationHours $userLocationDayHour
                     */
                    $userLocationDayHour = new UserLocationHours();
                    if(isset($openingDayHour['day'])){
                    $userLocationDayHour->setDayNumber($openingDayHour['day']);
                    }
                    if(isset($openingDayHour['hours'])){
                        $userLocationDayHour->setTimesTxt($openingDayHour['hours']);
                    }
                    $userLocationDayHour->setUserLocationData($userLocationData);
                    $em->persist($userLocationDayHour);
                }
                }

                $em->flush();

                if(isset($requestContent['image_primary'])){
                    if(strlen(serialize($requestContent['image_primary']))>100){
                    $mainfilename = 'main_'.parent::getUser()->getId().'_'.time().'.jpg';
                    $fs = new Filesystem();
                    if(!$fs->exists($this->get('kernel')->getRootDir() . '/../web/uploads/locations/'.$location->getId())){
                    $fs->mkdir($this->get('kernel')->getRootDir() . '/../web/uploads/locations/'.$location->getId());
                    }
                    file_put_contents($this->get('kernel')->getRootDir() . '/../web/uploads/locations/'.$location->getId().'/'.$mainfilename, base64_decode($requestContent['image_primary']));
                        $imagemanagerResponse = $this->container
                            ->get('liip_imagine.controller')
                            ->filterAction(
                                $this->getRequest(),
                                'locations/'.$location->getId().'/'.$mainfilename,      // original image you want to apply a filter to
                                'thumbnail'              // filter defined in config.yml
                            );
                        $imagemanagerResponse = $this->container
                            ->get('liip_imagine.controller')
                            ->filterAction(
                                $this->getRequest(),
                                'locations/'.$location->getId().'/'.$mainfilename,      // original image you want to apply a filter to
                                'fullsize'              // filter defined in config.yml
                            );
                    $location->setMainImageThumbnail($mainfilename);
                    $em->persist($location);
                    $em->flush();
                    $userLocationMedia = new UserLocationMedia($mainfilename);
                    $userLocationMedia->setIsmainimage(true);
                    $userLocationMedia->setMediaPath($mainfilename);
                    $userLocationMedia->setLocation($location);
                    $userLocationMedia->setUser(parent::getUser());
                    $em->persist($userLocationMedia);
                    $em->flush();
                    }
                }
                if(isset($requestContent['place_images'])){
                    $maxdelta=$this->getDoctrine()->getManager()->getRepository('GladturTagBundle:UserLocationMedia')->createQueryBuilder('MaxDelta')->select('MAX(MaxDelta.delta)')->where('MaxDelta.location='.$location->getId())->getQuery()->getSingleScalarResult();
                    if($maxdelta){
                        $delta = $maxdelta;
                    }
                    else{
                        $delta=0;
                    }

                    foreach($requestContent['place_images'] as $imageB64){
                        $userLocationMedia = new UserLocationMedia();
                        $userLocationMedia->setLocation($location);
                        $userLocationMedia->setUser(parent::getUser());
                        if(strlen(serialize($imageB64))>100){
                        $userLocationMedia->setDelta(++$delta);
                        $filename = $location->getId().'_'.$userLocationMedia->getDelta().'.jpg';
                        $fs = new Filesystem();

                        if(!$fs->exists($this->get('kernel')->getRootDir() . '/../web/uploads/'.$userLocationMedia->getMyULDirPath().'/'.$location->getId())){
                            $fs->mkdir($this->get('kernel')->getRootDir() . '/../web/uploads/'.$userLocationMedia->getMyULDirPath().'/'.$location->getId());
                        }
                        file_put_contents($this->get('kernel')->getRootDir() . '/../web/uploads/'.$userLocationMedia->getMyULDirPath().'/'.$location->getId().'/'.$filename, base64_decode($imageB64));
                            $imagemanagerResponse = $this->container
                                ->get('liip_imagine.controller')
                                ->filterAction(
                                    $this->getRequest(),
                                    'locations/'.$location->getId().'/'.$filename,      // original image you want to apply a filter to
                                    'thumbnail'              // filter defined in config.yml
                                );
                            $imagemanagerResponse = $this->container
                                ->get('liip_imagine.controller')
                                ->filterAction(
                                    $this->getRequest(),
                                    'locations/'.$location->getId().'/'.$filename,      // original image you want to apply a filter to
                                    'fullsize'              // filter defined in config.yml
                                );
                        $userLocationMedia->setMediaPath($filename);
                        }
                        //$location->addUserMedia($userLocationMedia);
                        $em->persist($userLocationMedia);
                        $em->flush();
                    }
                }
        if($location){
        $location->setPublished(true);
        if($name){
            $location->setReadableName($name);
        }
        if($topCatId){
            $tmpTopCategory = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\LocationCategory',$topCatId);
            $location->setTopCategory($tmpTopCategory);
        }
        //$location->addUpdatedByUser(parent::getUser());
        $em = $this->getDoctrine()->getManager();
            $slugtxt = $location->getReadableName().'-'.$location->getAddressStreetAndExtd();
            $location->slugify($slugtxt);
            $em->persist($location);
            $em->flush();
            /** Add the location to SOLR search-index, and we've already removed it in editing, so no need to check for edit/creation here! */
            /**
             * @var Client $solrClient
             */
            $update = $solrClient->createUpdate();
            $update->addDocument($location->toSolariumDocument($solrClient));
            $update->addCommit();
            $rs = $solrClient->update($update);
        $tmpTopCategory = null;
        $location_id = $location->getId();
        $success = 1;
        if(isset($requestContent['properties'])){
            $tagIdsMapValues = is_array($requestContent['properties'])?$requestContent['properties']:array();
            foreach($tagIdsMapValues as $tagId => $tagValue){
                $tag = $em->find('Gladtur\TagBundle\Entity\Tag', $tagId);
                $location = $em->find('Gladtur\TagBundle\Entity\Location', $location_id);
                if($tag){
                    $locationUserTag=$em->getRepository('GladturTagBundle:UserLocationTagData')->findOneBy(array('user'=>parent::getUser(), 'tag'=>$tag, 'location'=>$location));
                }
                if(!$locationUserTag){
                    $locationUserTag = new UserLocationTagData();
                    $locationUserTag->setUser(parent::getUser());
                    $locationUserTag->setLocation($location);
                    $locationUserTag->setTag($tag);
                }
                $locationUserTag->setUserProfile(parent::getUser()->getProfile());
                $locationUserTag->setTagValue($tagValue);
                $em->persist($locationUserTag);
                $em->flush();
            }
        }
        }
        }
        return parent::getJsonForData(array('success'=>$success, 'locationid'=>$location_id, 'op' => $op));
        }
    }

    /**
     * @Route("test/loctags")
     * @Method("GET")
     */
    public function locationTags($locationid){
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

    /**
     * @Route("location/addproperty")
     * @Method("POST")
     */
    public function locationSetTagAction(Request $request){
        $success = 0;
        if(parent::getIsJSON()){
           if(parent::getTokenPassed($request)){
            $requestContent = parent::getRequestFromJSON($request);
            $locationId = $requestContent['place_id'];
            $propertyId = $requestContent['property_id'];
            $propertyValue = $requestContent['value'];
            $profileId = (isset($requestContent['profileid']))?$requestContent['profileid']:false;
            if($profileId){
                $profile = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\TvguserProfile', $profileId);
            }
               else{
                   $profile = parent::getUser()->getProfile();
               }
                /**
                 * @var EntityManager $em
                 */
                $em = $this->getDoctrine()->getManager();
                $tag = $em->find('Gladtur\TagBundle\Entity\Tag', $propertyId);
                $location = $em->find('Gladtur\TagBundle\Entity\Location', $locationId);
                if($tag){
                    $locationUserTag=$em->getRepository('GladturTagBundle:UserLocationTagData')->findOneBy(array('user'=>parent::getUser(), 'tag'=>$tag, 'location'=>$location));
                }
            if(!$locationUserTag){
                $locationUserTag = new UserLocationTagData();
                $locationUserTag->setUser(parent::getUser());
                $locationUserTag->setLocation($location);
                $locationUserTag->setTag($tag);
            }
            $locationUserTag->setUserProfile($profile);
            $locationUserTag->setTagValue($propertyValue);
            $em->persist($locationUserTag);
            $em->flush();
            $success = 1;
            //$locationUserTagId = $locationUserTag->getId();
            }
        }
        return parent::getJsonForData(array('success'=>$success, 'data'=>$locationUserTag->getId()));
    }

    /**
     * @Route("location/addcomment")
     * @Method("POST")
     */
    public function locationSetCommentAction(Request $request){
        $success = 0;
        if(parent::getIsJSON()){
            if(parent::getTokenPassed($request)){
                $requestContent = parent::getRequestFromJSON($request);
                $locationId = $requestContent['place_id'];
                $commentTxt = (isset($requestContent['comment_txt']))?$requestContent['comment_txt']:'';
                $commentImagesAssoc = (isset($requestContent['comment_images']))?$requestContent['comment_images']:array();
                $commentImages = array();
                foreach($commentImagesAssoc as $key => $imagepath){
                    $commentImages[] = $imagepath;
                }
                $em = $this->getDoctrine()->getManager();
                /** @var Location $location */
                $location = $em->find('Gladtur\TagBundle\Entity\Location', $locationId);
                $locationUserComment = new UserLocationComments();
                $locationUserComment->setLocation($location);
                $locationUserComment->setUser(parent::getUser());
                $locationUserComment->setProfile(parent::getUser()->getProfile());
                $locationUserComment->setCommentTxt($commentTxt);
                $em->persist($locationUserComment);
                $em->flush();
                $maxdelta=$this->getDoctrine()->getManager()->getRepository('GladturTagBundle:CommentMedia')->createQueryBuilder('MaxDelta')->select('MAX(MaxDelta.delta)')->where('MaxDelta.comment='.$locationUserComment->getId())->getQuery()->getSingleScalarResult();
                if($maxdelta){
                    $delta = $maxdelta;
                }
                else{
                    $delta=0;
                }

                foreach($commentImages as $imageB64){
                    $locationUserCommentMedia = new CommentMedia();
                    $locationUserCommentMedia->setComment($locationUserComment);
                    if(strlen(serialize($imageB64))>100){
                    $locationUserCommentMedia->setDelta(++$delta);
                    $filename = $locationUserCommentMedia->getComment()->getId().'_'.$locationUserCommentMedia->getDelta().'.jpg';
                    file_put_contents($this->get('kernel')->getRootDir() . '/../web/uploads/'.$locationUserCommentMedia->getMyULDirPath().'/'.$filename, base64_decode($imageB64));
                        $imagemanagerResponse = $this->container
                            ->get('liip_imagine.controller')
                            ->filterAction(
                                $this->getRequest(),
                                $locationUserCommentMedia->getMyULDirPath().'/'.$filename,      // original image you want to apply a filter to
                                'thumbnail'              // filter defined in config.yml
                            );
                        $imagemanagerResponse = $this->container
                            ->get('liip_imagine.controller')
                            ->filterAction(
                                $this->getRequest(),
                                $locationUserCommentMedia->getMyULDirPath().'/'.$filename,      // original image you want to apply a filter to
                                'fullsize'              // filter defined in config.yml
                            );
                    $locationUserCommentMedia->setMediaPath($filename);
                    }
                    $em->persist($locationUserCommentMedia);
                }
                $em->flush();
                $success = 1;
            }
        }
        return parent::getJsonForData(array('success'=>$success));
    }

    /**
     * @Route("location/checkduplicate/{name}")
     * @Route("location/checkduplicate/{name}/lat/{lat}/lon/{lon}/radius/{radius}", defaults={"lat" = 0, "lon" = 0, "radius" = 500} )
     */
    public function locationCheckDuplicate($name, $lat = 0, $lon = 0, $radius = 500){
        $name = urldecode($name);
        // If latitude and longitude are not sent in the request, then the place has no duplicate candidates
        if(($lat == 0) && ($lon == 0)) {
            return parent::getJsonForData(array('success' => 1, 'places' => array()));
        }
        if($name !== ''){
          //return parent::getJsonForData((array) $searchRequest);
          $searchRs = $this->container->get('gladtur.api.search');
          return($searchRs->doSearch(array('profileid' => 1, 'query' => $name, 'latitude' => $lat, 'longitude' => $lon, 'radius' => $radius)));
        }
        return parent::getJsonForData(array('success' =>0, 'msg' => 'Insuffficient data sent in request to determine duplicate'));
    }
}