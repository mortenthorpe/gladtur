<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 7/11/13
 * Time: 9:32 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Gladtur\MobileBundle\Controller;

use Gladtur\TagBundle\Controller\JsonController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use \Doctrine\Common\Collections\Criteria;
use Solarium\QueryType\Select\Query\Query as Query;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


class SearchController extends JsonController{
    /**
     * @param Request $request
     * @Route("search", name="api_json_search")
     * @Method({"POST", "GET"})
     * http://wiki.solarium-project.org/index.php/V3:Building_a_select_query
     * http://stackoverflow.com/questions/8089947/solr-and-query-over-multiple-fields
     */
    public function solrSearchAction(Request $request){
        if(!parent::getIsJSON()) return parent::getJsonForData(array('success' => 0));
        $userProfileId = null;
        $requestAssoc = parent::getUnauthRequestFromJSON($request);
        return $this->doSearch($requestAssoc);
    }

    public function doSearch($requestAssoc = array()){
        $user = null;
        if(isset($requestAssoc['token'])) {
            $um = $this->container->get('fos_user.user_manager');
            $user = $um->findUserBy(array('salt'=>$requestAssoc['token']));
            $userProfileId = $user->getProfile()->getId();
        }
        else{
            $userProfileId = (isset($requestAssoc['profileid']))?$requestAssoc['profileid']:3;
        }
        $profile = $this->getDoctrine()->getRepository('GladTur\TagBundle\Entity\TvguserProfile')->find($userProfileId);
        $lat = (isset($requestAssoc['lat']))?$requestAssoc['lat']:null; /** optional parameter, floating point **/
        $lon = (isset($requestAssoc['lon']))?$requestAssoc['lon']:null; /** optional parameter, floating point **/
        $lat = (isset($requestAssoc['latitude']))?$requestAssoc['latitude']:$lat; /** optional parameter, floating point **/
        $lon = (isset($requestAssoc['longitude']))?$requestAssoc['longitude']:$lon; /** optional parameter, floating point **/
        $catid = (isset($requestAssoc['catid']))?$requestAssoc['catid']:null; /** optional parameter, floating point **/
        $searchQuery=isset($requestAssoc['query'])?$requestAssoc['query']:null;
        $page = isset($requestAssoc['p'])?intval($requestAssoc['p']):0;
        $perpage = isset($requestAssoc['count'])?intval($requestAssoc['count']):20;
        if($searchQuery){
            $searchQuery = str_replace(array('+', ' ', '&', '%'),array('\+','', '\&', 'pct'), $searchQuery);
            $searchQuery = str_replace(array('Ö','Æ','Ø','Å', 'ö', 'æ','ø','å','\u00c6','\u00d8','\u00c5','\u00e6','\u00f8','\u00e5'), array('OE','AE','OE','AA','oe','ae','oe','aa','AE','OE','AA','ae','oe','aa'), $searchQuery);
        }
        //if(!$searchQuery || (strlen($searchQuery)<3)) return parent::getJsonForData(array('success' => 0));
        $client = $this->container->get('solarium.client');
        $select = $client->createSelect();
        if($catid){
            // Make the search query sensitive to the category ID as well
            $select->setQuery('search_aggregate:*'.mb_strtolower($searchQuery).'* AND topcategory_id:'.$catid);
        }
        else{
            $select->setQuery('search_aggregate:*'.mb_strtolower($searchQuery).'*');
        }
        if($lat && $lon){
        // Sort by radial distance from reference lat,lon in SOLR, until a distance of $radius
        $selectHelper = $select->getHelper();
        $radius = isset($requestAssoc['radius'])?($requestAssoc['radius']/1000):1000; // Distance for radial search in Kilometers, covers Denmark, so OK with 1000.
          $select->createFilterQuery('distance')->setQuery($selectHelper->geofilt('location', $lat, $lon, $radius));
          $select->addSort('geodist(location,'. $lat .','.$lon.')', Query::SORT_ASC);
        }
        $select->setStart($page*$perpage)->setRows($perpage);
        $results = $client->select($select);
        $rsString='';
        $locations = array();
        foreach($results as $document){
            foreach($document as $field => $value){
                if(is_array($value)) continue;
                if($field == 'id'){
                    $rsString.='@'.$field.': '.$value.', ';
                }
                if($field == 'id'){
                    $locations[] = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\Location', intval($value));
                }
            }
        }
        if(count($locations)){
            $locationsHelper = $this->get('gladtur.location.pagedlocations');
            $locationsData = $locationsHelper->getPagedLocations($locations, 0, 999, $lat, $lon, $profile, $user);
            $locationsData = array_merge(array('places'=>$locationsData), array('totalcount'=>count($locations)), array('query'=>$searchQuery));
            return parent::getJsonForData($locationsData);
        }
        if(empty($locationsData)){
            return parent::getJsonForData(array('success'=>0, 'query' => $searchQuery, 'totalcount'=>0));
        }
    }
}