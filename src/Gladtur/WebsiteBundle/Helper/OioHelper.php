<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 8/5/13
 * Time: 2:42 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Gladtur\WebsiteBundle\Helper;


class OioHelper {
    public function cityNames($livedata=true){
        $citiesAssoc=array();
        if($livedata){
            $citynamesZipcodesJSON = file_get_contents('http://geo.oiorest.dk/postnumre.json');
            $citynamesZipcodesAssoc = json_decode($citynamesZipcodesJSON, true);
            foreach($citynamesZipcodesAssoc as $nameAndZip){
                $citiesAssoc[$nameAndZip['nr']] = array('fra'=>$nameAndZip['fra'], 'til'=>$nameAndZip['til'],'navn'=>$nameAndZip['navn']);
            }
        }
        return $citiesAssoc;
    }
}