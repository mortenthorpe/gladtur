<?php
/**
 * Created by PhpStorm.
 * User: mortenthorpe
 * Date: 07/07/14
 * Time: 09.50
 */

namespace Gladtur\TagBundle\Entity;


class Address
{
    private $datasource;
    private $road_name;
    private $zipcode_precise;
    private $zipcode_from;
    private $zipcode_to;
    private $commune_code;
    private $commune_name;

    public function __construct($datasource = null, $requested_road_name = null)
    {
        $this->datasource = $datasource;

        return $this;
    }

    /**
     * @param string $roadname_req
     * @return mixed
     * Returns numerically indexed associative array of structured candidates for roads in differnt communes, data-keys for elements are:
     * streetname, zipcode, lat, lon
     */
    public function road_data_from_roadname_req($roadname_req = null)
    {
        $response_road_names_assoc = array();
        if ($roadname_req) {
            $response_road_data_json = file_get_contents($this->datasource . '?vejnavn = ' . $roadname_req);
            $response_road_data_assoc = json_decode($response_road_data_json, true);
            foreach ($response_road_data_assoc as $road_data) {
                $response_road_names_assoc[$road_data['kode']] = array(
                    'streetname' => $road_data['navn'],
                    'zipcode' => $road_data['postnummer']['nr'],
                    'lat' => $road_data['wgs84koordinat']['bredde'],
                    'lon' => $road_data['wgs84koordinat']['længde']
                );
            }
        }

        return $response_road_names_assoc;
    }
}