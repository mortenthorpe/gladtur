<?php

namespace Gladtur\TagBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Gladtur\TagBundle\Entity\LocationCategory
 *
 * @ORM\Table(name="location_category")
 * @ORM\Entity
 */
class LocationCategory
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
    * @var integer $parentCategoryId
    * @ORM\Column(name="parent_category_id", type="integer", length=11, nullable=true)
    */
    private $parentCategory;
	
    /**
    * @ORM\ManyToOne(targetEntity="User", inversedBy="locationCategory")
    **/
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="Location", inversedBy="locationCategory")
     **/
    private $location;

        public function getUserLocationData(){
            return $this->user_location_data;
        }
    /**
     * @var string $readableName
     *
     * @ORM\Column(name="readable_name", type="string", length=255, nullable=true)
     */
    private $readableName;

    /**
     * @var boolean $published
     *
     * @ORM\Column(name="published", type="boolean", nullable=true)
     */
    private $published;


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
     * Set readableName
     *
     * @param string $readableName
     * @return LocationCategory
     */
    public function setReadableName($readableName)
    {
        $this->readableName = $readableName;
    
        return $this;
    }

    /**
     * Get readableName
     *
     * @return string 
     */
    public function getReadableName()
    {
        return $this->readableName;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return Location
     */
    public function setPublished($published)
    {
        $this->published = $published;
    
        return $this;
    }

    /**
     * Get published
     *
     * @return boolean 
     */
    public function getPublished()
    {
        return $this->published;
    }

	public function mtGetPublishedTxt(){
		return ($this->published)?'Ja':'Nej';
	}
        
    
	public function __toString(){
		return $this->getReadableName();
	}

    /**
     * Set parentCategory
     *
     * @param integer $parentCategory
     * @return LocationCategory
     */
    public function setParentCategory($parentCategory)
    {
        $this->parentCategory = $parentCategory;
    
        return $this;
    }

    /**
     * Get parentCategory
     *
     * @return integer 
     */
    public function getParentCategory()
    {
        return $this->parentCategory;
    }
}
