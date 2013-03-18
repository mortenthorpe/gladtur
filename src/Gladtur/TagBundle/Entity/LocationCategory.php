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
//http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html //
    /**
     * @ORM\OneToMany(targetEntity="LocationCategory", mappedBy="parentCategory")
     **/
    private $childCategories;


    public function getChildCategories(){
        return $this->childCategories;
    }

    /**
     * @ORM\ManyToOne(targetEntity="LocationCategory", inversedBy="childCategories")
     **/
    private $parentCategory;

    public function getParentCategory(){
        return ($this->parentCategory)?$this->parentCategory:null;
    }


    /**
     * @ORM\ManyToMany(targetEntity="Location", mappedBy="locationCategory")
     **/
    private $location;


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


    public function __construct() {
        $this->childCategories = new \Doctrine\Common\Collections\ArrayCollection();
    }

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

    public function getNestedReadableName(){
        if($this->getParentCategory()) return ' - '.$this->getReadableName();
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

    public function getLocationCategoryUnassigned($categoryPublished=true){

    }

}
