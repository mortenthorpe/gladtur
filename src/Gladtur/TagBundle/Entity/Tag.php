<?php

namespace Gladtur\TagBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Gladtur\TagBundle\Entity\Tag
 *
 * @ORM\Table(name="tag")
 * @ORM\Entity
 */
class Tag
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
     * @var boolean $published
     *
     * @ORM\Column(name="published", type="boolean", nullable=true)
     */
    private $published;

    /**
     * @var string $readableName
     *
     * @ORM\Column(name="readable_name", type="string", length=255, nullable=true)
     */
    private $readableName;

    /**
     * @var string $textDescription
     *
     * @ORM\Column(name="text_description", type="text", nullable=true)
     */
    private $textDescription;

    /**
     * @var TagCategory
     *
     * @ORM\ManyToOne(targetEntity="TagCategory")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tag_category_id", referencedColumnName="id")
     * })
     */
    private $tagCategory;

    /**
     * @var TagCategory
     *
     * @ORM\ManyToOne(targetEntity="TagCategory")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tag_category_id", referencedColumnName="id")
     * })
     */

    /**
     * @var integer $user
     * @ORM\OneToOne(targetEntity="UserLocationTagData", mappedBy="tag")
     */
    protected $userLocationTagData;
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
     * Set published
     *
     * @param boolean $published
     * @return Tag
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

    /**
     * Set readableName
     *
     * @param string $readableName
     * @return Tag
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
     * Set textDescription
     *
     * @param string $textDescription
     * @return Tag
     */
    public function setTextDescription($textDescription)
    {
        $this->textDescription = $textDescription;
    
        return $this;
    }

    /**
     * Get textDescription
     *
     * @return string 
     */
    public function getTextDescription()
    {
        return $this->textDescription;
    }

    /**
     * Set tagCategory
     *
     * @param Gladtur\TagBundle\Entity\TagCategory $tagCategory
     * @return Tag
     */
    public function setTagCategory(\Gladtur\TagBundle\Entity\TagCategory $tagCategory = null)
    {
        $this->tagCategory = $tagCategory;
    
        return $this;
    }

    /**
     * Get tagCategory
     *
     * @return Gladtur\TagBundle\Entity\TagCategory 
     */
    public function getTagCategory()
    {
        return $this->tagCategory;
    }
	
	/**
	* Set profilesRelevance
	*
	* @param array profilesRelevance
	* @return Tag
	*/
	public function setProfilesRelevance($profilesRelevance)
	{
		$this->profilesRelevance = $profilesRelevance;
		return $this;
	}
	
	/**
	* Get profilesRelevance
	*
	* @return array
	*/
	public function getRelevance()
	{
		return $this->Relevance;
	}
	
	public function __toString(){
		return $this->getReadableName();
	}
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->profiles = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add profiles
     *
     * @param Gladtur\TagBundle\Entity\TvguserProfile $profiles
     * @return Tag
     */
    public function addProfile(\Gladtur\TagBundle\Entity\TvguserProfile $profiles)
    {
        $this->profiles[] = $profiles;
    
        return $this;
    }

    /**
     * Remove profiles
     *
     * @param Gladtur\TagBundle\Entity\TvguserProfile $profiles
     */
    public function removeProfile(\Gladtur\TagBundle\Entity\TvguserProfile $profiles)
    {
        $this->profiles->removeElement($profiles);
    }

    /**
     * Get profiles
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getProfiles()
    {
        return $this->profiles;
    }
}