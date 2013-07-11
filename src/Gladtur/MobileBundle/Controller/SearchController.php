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
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use \Doctrine\Common\Collections\Criteria;


class SearchController extends JsonController{
    /**
     * @param Request $request
     * @Route("mobile/search", name="api_json_search")
     */
    public function solrSearchAction(Request $request){
        if(!parent::getIsJSON()) return parent::getJsonForData(array('success' => 0));
        $requestAssoc = parent::getRequestFromJSON($request);
        $lat = (isset($requestAssoc['lat']))?$requestAssoc['lat']:null; /** optional parameter, floating point **/
        $lon = (isset($requestAssoc['lon']))?$requestAssoc['lon']:null; /** optional parameter, floating point **/
        $searchQuery=isset($requestAssoc['query'])?$requestAssoc['query']:null;
        $locationsData = null;
        if(!$searchQuery){
            $success = 0;
            return parent::getJsonForData(array('success' => $success, 'query' => '', 'results' => $locationsData));
        }
        if(!$lat || !$lon){
            // Return search results only ranked by relevance //
            $locationsData = array(
                array(
                    'id' => 1,
                    'topcatid' => 10,
                    'name' => 'Meyers Madhus',
                    'address' => array('zip' => '1705 KBH K', 'streetname' => 'Gl. Kongevej 56, stuen'),
                    'score' => 1,
                    'lat' => 55.963999,
                    'lon' => 12.281562,
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
                    'distance' => -1,
                    'thumbnail' => 'http://morning.dk/morning.hyperesources/morningnetwork-webV2.png'
                )
            );
            $success = 1;
        }
        else{
            /** Return search results ranked by...:
             * 1. Relevance ... then subranked by ...
             * 2. Current vicinity
             **/
            $locationsData = array(
                array(
                    'id' => 1,
                    'topcatid' => 10,
                    'name' => 'Meyers Madhus',
                    'address' => array('zip' => '1705 KBH K', 'streetname' => 'Gl. Kongevej 56, stuen'),
                    'score' => 1,
                    'lat' => 55.963999,
                    'lon' => 12.281562,
                    'distance' => 1200,
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
            $success = 1;
        }
        return parent::getJsonForData(array('success' => $success, 'query' => $searchQuery, 'results' => $locationsData));
    }
}