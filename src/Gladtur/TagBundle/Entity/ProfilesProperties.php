<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 6/6/13
 * Time: 12:29 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Gladtur\TagBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ProfilesProperties
 * @package Gladtur\TagBundle\Entity
 */
class ProfilesProperties
{
    private $id;
    private $profile_id;
    private $property_id;
    private $location_id;
    private $property_value;

    /**
     * @param mixed $property_value
     */
    public function setPropertyValue($property_value)
    {
        $this->property_value = $property_value;
    }

    /**
     * @return mixed
     */
    public function getPropertyValue()
    {
        return $this->property_value;
    }

}