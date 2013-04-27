<?php
namespace Gladtur\TagBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Gladtur\TagBundle\Entity\Location
 *
 * @ORM\Table(name="location")
 * @ORM\Entity
 */
class Location
{

    private $locDataInstance;

    /**
     * @ORM\ManyToMany(targetEntity="LocationCategory", inversedBy="location")
     **/

    private $locationCategory;

    /**
     * @ORM\OneToMany(targetEntity="UserLocationTagData", mappedBy="location")
     */
    protected $userLocationTagData;

    public function __construct(){
        $this->locationCategory = new \Doctrine\Common\Collections\ArrayCollection();
        $this->userLocationData=new \Doctrine\Common\Collections\ArrayCollection();
        $this->setPublished(true);
    }
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
     * @var string $homepage
     *
     * @ORM\Column(name="homepage", type="string", length=255, nullable=true)
     */
    private $homepage;

    // http://docs.doctrine-project.org/projects/doctrine-orm/en/2.0.x/reference/association-mapping.html#many-to-many-unidirectional
    /**
    * @var string $UserLocationData
     * Owning Side
     *
     * @ORM\OneToMany(targetEntity="UserLocationData", mappedBy="location")
     * @ORM\OrderBy({"created_at" = "DESC"})
     */
    private $userLocationData;
        
    public function getUserLocationData($latestOnly = true){
       if($latestOnly){
           return $this->userLocationData->first();
       }
       return $this->userLocationData;
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

    public function mtGetAddressZip()
    {
        return ($this->getAddressZip())?$this->getAddressZip():'----';
    }

    /**
     * Set homepage
     *
     * @param string $homepage
     * @return Location
     */
    public function setHomepage($homepage)
    {
        $this->homepage = $homepage;
    
        return $this;
    }

    /**
     * Get homepage
     *
     * @return string 
     */
    public function getHomepage()
    {
        return $this->homepage;
    }


    /**
     * @return string
     */
    public function __toString(){
        $this->getUserLocationData()->getReadableName();
	}

    /**
     * @return Doctrine\Common\Collections\Collection
     */
    public function getLocationCategory(){
        return $this->locationCategory;
    }

    /**
     * @param LocationCategory $locationCategories
     * @return Location
     */
    public function addLocationcategory(\Gladtur\TagBundle\Entity\LocationCategory $locationCategories)
    {
        $this->locationCategory[] = $locationCategories;

        return $this;
    }


    public function removeLocationcategory(\Gladtur\TagBundle\Entity\LocationCategory $locationCategories)
    {
        $this->locationCategory->removeElement($locationCategories);
    }

    public function getLocationCategories(){
        return $this->locationCategory;
    }

    public function getAddressCountryReadable(){
        return 'Denmark';
    }

    public function __call($name, $arguments=null){
        if(strpos($name, 'get') !== 0){
          $name = 'get'.ucfirst($name);
        }

        if(!isset($this->locDataInstance)){
            $this->locDataInstance = $this->getUserLocationData();
        }
        if(isset($this->locDataInstance) && $this->locDataInstance){
            return $this->locDataInstance->$name($arguments);
        }
        return new UserLocationData();
    }
}