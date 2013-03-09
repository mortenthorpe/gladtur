<?php

namespace Gladtur\TagBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Gladtur\TagBundle\Entity\LocationTag
 *
 * @ORM\Table(name="location_tag")
 * @ORM\Entity
 */
class LocationTag
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
     * @var string $locationTagProperties
     *
     * @ORM\Column(name="location_tag_properties", type="text", nullable=true)
     */
    private $locationTagProperties;

    /**
     * @var Tag
     *
     * @ORM\ManyToOne(targetEntity="Tag")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tag_id", referencedColumnName="id")
     * })
     */
    private $tag;



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
     * Set locationTagProperties
     *
     * @param string $locationTagProperties
     * @return LocationTag
     */
    public function setLocationTagProperties($locationTagProperties)
    {
        $this->locationTagProperties = $locationTagProperties;
    
        return $this;
    }

    /**
     * Get locationTagProperties
     *
     * @return string 
     */
    public function getLocationTagProperties()
    {
        return $this->locationTagProperties;
    }

    /**
     * Set tag
     *
     * @param Gladtur\TagBundle\Entity\Tag $tag
     * @return LocationTag
     */
    public function setTag(\Gladtur\TagBundle\Entity\Tag $tag = null)
    {
        $this->tag = $tag;
    
        return $this;
    }

    /**
     * Get tag
     *
     * @return Gladtur\TagBundle\Entity\Tag 
     */
    public function getTag()
    {
        return $this->tag;
    }
}