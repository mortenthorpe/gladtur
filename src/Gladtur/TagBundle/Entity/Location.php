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
    /**
     * @ORM\ManyToMany(targetEntity="LocationCategory", mappedBy="location")
     **/
    private $locationCategory;
    /**
     * @ORM\OneToMany(targetEntity="UserLocationTagData", mappedBy="location")
     */
    protected $userLocationTagData;

    public function __construct(){
        $this->userLocationData=new \Doctrine\Common\Collections\ArrayCollection();
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
      * @var string $latitude
      *
      * @ORM\Column(name="latitude", type="string", length=64, nullable=true)
      */
      private $latitude;
      /**
       * @var string $longitude
       *
       * @ORM\Column(name="longitude", type="string", length=64, nullable=true)
       */
       private $longitude;
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
     * @var string $addressZip
     *
     * @ORM\Column(name="address_zip", type="string", length=20, nullable=true)
     */
    private $addressZip;

    /**
     * @var string $addressCountry
     *
     * @ORM\Column(name="address_country", type="string", length=255, nullable=true)
     */
    private $addressCountry;

    /**
     * @var string $addressCity
     *
     * @ORM\Column(name="address_city", type="string", length=255, nullable=true)
     */
    private $addressCity;

    /**
     * @var string $addressStreet
     *
     * @ORM\Column(name="address_street", type="string", length=255, nullable=true)
     */
    private $addressStreet;

    /**
     * @var string $addressExtd
     *
     * @ORM\Column(name="address_extd", type="string", length=255, nullable=true)
     */
    private $addressExtd;

    /**
     * @var string $phone
     *
     * @ORM\Column(name="phone", type="string", length=20, nullable=true)
     */
    private $phone;

    /**
     * @var string $mail
     *
     * @ORM\Column(name="mail", type="string", length=255, nullable=true)
     */
    private $mail;

    /**
     * @var string $homepage
     *
     * @ORM\Column(name="homepage", type="string", length=255, nullable=true)
     */
    private $homepage;

    /**
     * @var string $contactPerson
     *
     * @ORM\Column(name="contact_person", type="string", length=255, nullable=true)
     */
    private $contactPerson;

    /**
     * @var string $mediapath
     *
     * @ORM\Column(name="mediapath", type="string", length=255, nullable=true)
     */
    private $mediapath;

    // http://docs.doctrine-project.org/projects/doctrine-orm/en/2.0.x/reference/association-mapping.html#many-to-many-unidirectional
    /**
    * @var string $UserLocationData
     * Owning Side
     *
     * @ORM\OneToMany(targetEntity="UserLocationData", mappedBy="location")
     */
        private $userLocationData;
        
        public function getUserLocationData(){
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
     * Set readableName
     *
     * @param string $readableName
     * @return Location
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
    /**
     * Set addressZip
     *
     * @param string $addressZip
     * @return Location
     */
    public function setAddressZip($addressZip)
    {
        $this->addressZip = $addressZip;
    
        return $this;
    }

    /**
     * Get addressZip
     *
     * @return string 
     */
    public function getAddressZip()
    {
        return $this->addressZip;
    }

    public function mtGetAddressZip()
    {
        return ($this->addressZip)?$this->addressZip:'----';
    }

    /**
     * Set addressCountry
     *
     * @param string $addressCountry
     * @return Location
     */
    public function setAddressCountry($addressCountry)
    {
        $this->addressCountry = $addressCountry;
    
        return $this;
    }

    /**
     * Get addressCountry
     *
     * @return string 
     */
    public function getAddressCountry()
    {
        return $this->addressCountry;
    }

    /**
     * Set addressCity
     *
     * @param string $addressCity
     * @return Location
     */
    public function setAddressCity($addressCity)
    {
        $this->addressCity = $addressCity;
    
        return $this;
    }

    /**
     * Get addressCity
     *
     * @return string 
     */
    public function getAddressCity()
    {
        return $this->addressCity;
    }

    /**
     * Set addressStreet
     *
     * @param string $addressStreet
     * @return Location
     */
    public function setAddressStreet($addressStreet)
    {
        $this->addressStreet = $addressStreet;
    
        return $this;
    }

    /**
     * Get addressStreet
     *
     * @return string 
     */
    public function getAddressStreet()
    {
        return $this->addressStreet;
    }

    /**
     * Set addressExtd
     *
     * @param string $addressExtd
     * @return Location
     */
    public function setAddressExtd($addressExtd)
    {
        $this->addressExtd = $addressExtd;
    
        return $this;
    }

    /**
     * Get addressExtd
     *
     * @return string 
     */
    public function getAddressExtd()
    {
        return $this->addressExtd;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Location
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    
        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set mail
     *
     * @param string $mail
     * @return Location
     */
    public function setMail($mail)
    {
        $this->mail = $mail;
    
        return $this;
    }

    /**
     * Get mail
     *
     * @return string 
     */
    public function getMail()
    {
        return $this->mail;
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
     * Set contactPerson
     *
     * @param string $contactPerson
     * @return Location
     */
    public function setContactPerson($contactPerson)
    {
        $this->contactPerson = $contactPerson;
    
        return $this;
    }

    /**
     * Get contactPerson
     *
     * @return string 
     */
    public function getContactPerson()
    {
        return $this->contactPerson;
    }

    /**
     * Set mediapath
     *
     * @param string $mediapath
     * @return Location
     */
    public function setMediapath($mediapath)
    {
        $this->mediapath = $mediapath;
    
        return $this;
    }

    /**
     * Get mediapath
     *
     * @return string 
     */
    public function getMediapath()
    {
        return $this->mediapath;
    }
	
    
    public function setLatitude($latitude){
        $this->latitude=$latitude;
        return $this;
    }
    
    public function getLatitude(){
        return $this->latitude;
    }
    
    public function setLongitude($longitude){
        $this->longitude=$longitude;
        return $this;
    }
    
    public function getLongitude(){
        return $this->longitude;
    }
    
	public function __toString(){
		return $this->getReadableName();
	}
    
}