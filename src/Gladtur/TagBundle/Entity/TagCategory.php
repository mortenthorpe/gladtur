<?php

namespace Gladtur\TagBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File as FILE;

/**
 * Gladtur\TagBundle\Entity\TagCategory
 *
 * @ORM\Table(name="tag_category")
 * @ORM\Entity
 */
class TagCategory
{
    /**
     * @var integer $id
     *
     * @ORM\Column( name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer $catid
     *
     * @ORM\Column(name="catid", type="integer", nullable=true)
     */
    private $catid;

    /**
        * @ORM\ManyToMany(targetEntity="TvguserProfile", mappedBy="tagCategories")
        */
       private $profiles;
       
    /**
     * @var integer $published
     *
     * @ORM\Column(name="published", type="boolean", nullable=true)
     */
    private $published;

    /**
     * @var boolean $isGeneral
     *
     * @ORM\Column(name="is_general", type="boolean", nullable=true)
     */
    private $isGeneral;

    /**
     * @var string $readableName
     *
     * @ORM\Column(name="readable_name", type="string", length=255, nullable=true)
     */
    private $readableName;

    /**
     * @var integer $weight
     *
     * @ORM\Column(name="weight", type="integer", nullable=true)
     */
    private $weight;

    /**
     * @var string $iconFilepath
     *
     * @ORM\Column(name="icon_filepath", type="string", length=255, nullable=true)
     */
    private $iconFilepath;
    /**
     * @var string $textDescription
     *
     * @ORM\Column(name="text_description", type="text", nullable=true)
     */
	private $textDescription;

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
     * Set catid
     *
     * @param integer $catid
     * @return TagCategory
     */
    public function setCatid($catid)
    {
        $this->catid = $catid;
    
        return $this;
    }

    /**
     * Get catid
     *
     * @return integer 
     */
    public function getCatid()
    {
        return $this->catid;
    }

    /**
     * Set published
     *
     * @param integer $published
     * @return TagCategory
     */
    public function setPublished($published)
    {
        $this->published = $published;
    
        return $this;
    }

    /**
     * Get published
     *
     * @return integer 
     */
    public function getPublished()
    {
        return $this->published;
    }

	public function mtGetPublishedTxt(){
		return ($this->published)?'Ja':'Nej';
	}
	
	public function mtGetIsGeneralTxt(){
		return ($this->isGeneral)?'Ja':'Nej';
	}
    /**
     * Set isGeneral
     *
     * @param boolean $isGeneral
     * @return TagCategory
     */
    public function setIsGeneral($isGeneral)
    {
        $this->isGeneral = $isGeneral;
    
        return $this;
    }

    /**
     * Get isGeneral
     *
     * @return boolean 
     */
    public function getIsGeneral()
    {
        return $this->isGeneral;
    }

    /**
     * Set readableName
     *
     * @param string $readableName
     * @return TagCategory
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
     * Set weight
     *
     * @param integer $weight
     * @return TagCategory
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    
        return $this;
    }

    /**
     * Get weight
     *
     * @return integer 
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set iconFilepath
     *
     * @param string $iconFilepath
     * @return TagCategory
     */
    public function setIconFilepath($iconFilepath)
    {
        $this->iconFilepath = $iconFilepath;
    
        return $this;
    }

    /**
     * Get iconFilepath
     *
     * @return string 
     */
    public function getIconFilepath()
    {
        return $this->iconFilepath;
    }

    public function getIconFile()
    {
        return new File($this->iconFilepath);
    }
	/**
	* Set textDescription
	*
	* @param string $textDescription
	* @return $TagCategory
	*/
	public function setTextDescription($textDescription){
		return $this->textDescription=$textDescription;
		return $this;
	}
	/**
	* Get textDescription
	*
	* @return string
	*/
	public function getTextDescription(){
		return $this->textDescription;
	}
	
	public function __toString(){
		return $this->getReadableName();
	}
}