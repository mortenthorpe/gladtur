<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 8/6/13
 * Time: 12:16 PM
 * To change this template use File | Settings | File Templates.
 */
namespace Gladtur\WebsiteBundle\Helper;

use Doctrine\ORM\EntityManager;

class LocationHelper{
    private $em;
    public function __construct(EntityManager $em){
        $this->em = $em;
    }

    public function locationFromSlug($slugString){
        /**
         * Slug syntax is a URL segments string:
         * $location->getAddressZip().'/'.$this->getStreetNameClean().'/'.$this->getNameClean();
         */
        $this->em->createQuery('select l from Gladtur\TagBundle\Entity\Location where l.slug='.$slugString);
    }
}