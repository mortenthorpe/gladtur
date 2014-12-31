<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 7/28/13
 * Time: 10:22 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Gladtur\MobileBundle\Helper;


use Doctrine\ORM\EntityManager;
use Gladtur\TagBundle\Entity\Location;


class LocationHelper{
    private $entityManager;

    /*public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }*/

    private $container;

    public function __construct($container, $entityManager){
        $this->container = $container;
        $this->entityManager = $entityManager;
    }

    public function getPagedLocations($locationsArr, $page=0, $itemsPerPage=10, $lat=0, $lon=0, $profile=null,$user=null) {
        $manualSort = false;
        $locationsDataRaw = array();
        $locationsData = array();
        $locationsIdxGeoCoords = array();
        $locationIdxDistances = array();
        /**
         * @var Location $location
         */
        $avalancheService = $this->container->get('liip_imagine.cache.manager');
        foreach($locationsArr as $location){
            $thumbnailImagePath = '';
            if($location->getMainImageThumbnail()){
                $thumbnailImagePath = $avalancheService->getBrowserPath('locations/'.$location->getId().'/'.$location->getMainImageThumbnail(), 'thumbnail', true);
            }
            else{
                $thumbnailImagePath = $avalancheService->getBrowserPath('noimage.png', 'thumbnail', true);
            }

            $tagsandValues = $this->_helperlocationTagsandValues($location->getId(), $profile->getId(), $user);
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

            if(($lat>0) && ($lon>0)){
                // The Request contains geo-coordinate data! //
                $manualSort=true;
                if($location->getLatitude() && $location->getLongitude()){
                    $locDistance=abs($this->haversineGreatCircleDistance($location->getLatitude(), $location->getLongitude(), $lat, $lon));
                }
                else{
                    $locDistance = -1;
                }
                $locationsIdxGeoCoords[$location->getId().' '] = array('id'=>$location->getId(),'topcatid'=>$location->getTopCategory()->getId(), 'name'=>$location->getReadableName(),'score'=>$score, 'distance'=>round($locDistance, 0), 'thumbnail' => $thumbnailImagePath, 'address' => $location->getAddressAssoc(), 'lat'=>$location->getLatitude(), 'lon'=>$location->getLongitude());
                $locationIdxDistances[$location->getId().' '] = $locDistance;// The id=>distance mapping ! //
            }

            else{
                $manualSort=false;
                $locationsIdxGeoCoords[$location->getId().' '] = array('id'=>$location->getId(), 'topcatid'=>$location->getTopCategory()->getId(), 'name'=>$location->getReadableName(),'score'=>$score, 'distance'=>-1, 'thumbnail'=>$thumbnailImagePath, 'address' => $location->getAddressAssoc(), 'lat'=>$location->getLatitude(), 'lon'=>$location->getLongitude());
            }
        }

        if($manualSort){
            asort($locationIdxDistances, SORT_NUMERIC); // Sorted by distance ascending ! //
            foreach($locationIdxDistances as $locId => $locDistance){
                if($locDistance>=0){
                    $locationsDataRaw[] = $locationsIdxGeoCoords[$locId];
                }
            }
        }

        else{
            $locationsDataRaw = array_values($locationsIdxGeoCoords);
        }
        if(count($locationsIdxGeoCoords)>$itemsPerPage){
            //for($i = ($page-1)*$itemsPerPage; $i<=($page*$itemsPerPage)-1; $i++){
            for($i = $page*$itemsPerPage; $i<=(($page*$itemsPerPage)+($itemsPerPage-1)); $i++){
                if(isset($locationsDataRaw[$i])){
                    $locationsData[] = $locationsDataRaw[$i];//array_shift($locationsDataRaw);
                }
            }
        }
        else{
            $locationsData = $locationsDataRaw;
        }
        return $locationsData;
    }

    /**
     * See http://stackoverflow.com/questions/14750275/haversine-formula-with-php
     * @param $latitudeFrom
     * @param $longitudeFrom
     * @param $latitudeTo
     * @param $longitudeTo
     * @param int $earthRadius
     * @return int
     */
    public function haversineGreatCircleDistance(
        $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
    {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                    cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    public function _helperlocationTagsandValues($locationid, $profileid, $user = null){
        $profileTagsRs = array();
        // Get the profile from a local selection first, and if not set then from the active user.
        $location = $this->entityManager->getRepository('Gladtur\TagBundle\Entity\Location')->find($locationid);
        $profile = ($profileid)?$this->entityManager->getRepository('Gladtur\TagBundle\Entity\TvguserProfile')->find($profileid):$user->getProfile();
        $profileqb = $this->entityManager->createQueryBuilder('profiletags');
        $tagvaluesqb = $this->entityManager->createQueryBuilder('tagvalues');
        if(!$profile->getIndividualized()){
            // Get all tags for a general non-individualized profile
            //$profileTagsQb = $profileqb->select(array('profiletags.id id', 'profiletags.readableName', 'profiletags.textDescription', 'profiletags.iconPath icon'))->from('Gladtur\TagBundle\Entity\TvguserProfile', 'uprofile')->join('uprofile.tags', 'profiletags')->join('profiletags.location_categories', 'tag_locationcategories')/*->join('uprofile.userLocationTagData', 'loctagdata')*/->where('uprofile.id = '.$profileid)/*->andWhere('loctagdata.tagvalue IN (1,2)')*/;
            $userTagsRs = $profile->getTags($location->getTopCategory()->getId());
        }
        else{
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
}