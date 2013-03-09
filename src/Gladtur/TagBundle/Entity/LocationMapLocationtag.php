<?php

namespace Gladtur\TagBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Gladtur\TagBundle\Entity\LocationMapLocationtag
 *
 * @ORM\Table(name="location_map_locationtag")
 * @ORM\Entity
 */
class LocationMapLocationtag
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Location
     *
     * @ORM\ManyToOne(targetEntity="Location")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="location_id", referencedColumnName="id")
     * })
     */
    private $location;

    /**
     * @var LocationTag
     *
     * @ORM\ManyToOne(targetEntity="LocationTag")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="location_tag_id", referencedColumnName="id")
     * })
     */
    private $locationTag;



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set location
     *
     * @param Gladtur\TagBundle\Entity\Location $location
     * @return LocationMapLocationtag
     */
    public function setLocation(\Gladtur\TagBundle\Entity\Location $location = null)
    {
        $this->location = $location;
    
        return $this;
    }

    /**
     * Get location
     *
     * @return Gladtur\TagBundle\Entity\Location 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set locationTag
     *
     * @param Gladtur\TagBundle\Entity\LocationTag $locationTag
     * @return LocationMapLocationtag
     */
    public function setLocationTag(\Gladtur\TagBundle\Entity\LocationTag $locationTag = null)
    {
        $this->locationTag = $locationTag;
    
        return $this;
    }

    /**
     * Get locationTag
     *
     * @return Gladtur\TagBundle\Entity\LocationTag 
     */
    public function getLocationTag()
    {
        return $this->locationTag;
    }
}