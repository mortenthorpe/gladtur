<?php
namespace Gladtur\TagBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Properties declared here are STATIC to the location, any mutable properties are declared inside the UserLocationData Class, as the mutability consists of changing users providing differing property-data for a specific location.
 * Static properties (the ones in this Class) have values assigned by the first user, or overridden by an ADMINISTRATOR type actor
 */

/**
 * Gladtur\TagBundle\Entity\Location
 *
 * @ORM\Table(name="location")
 * @ORM\Entity
 */
class Location
{

    /**
     * @ORM\OneToMany(targetEntity="UserLocationTagData", mappedBy="location")
     * @var UserLocationTagData $userLocationTagData
     */
    protected $userLocationTagData;

    /**
     * @var UserLocationData $locDataInstance
     */
    private $locData;
    /**
     * @ORM\ManyToMany(targetEntity="user", inversedBy="location", fetch="EXTRA_LAZY")
     * @var User $updatedByUser
     */
    private $updatedByUser;
    /**
     * @var string $readableName
     *
     * @ORM\Column(name="readable_name", type="string", length=255, nullable=true)
     */
    private $readableName;

    /**
     * @param \Gladtur\TagBundle\Entity\UserLocationData $locData
     */
    public function setLocData($locData)
    {
        $this->locData = $locData;
    }

    /**
     * @return \Gladtur\TagBundle\Entity\UserLocationData
     */
    public function getLocData()
    {
        return $this->locData;
    }

    /**
     * @param string $readableName
     */
    public function setReadableName($readableName)
    {
        $this->readableName = $readableName;
    }

    /**
     * @return string
     */
    public function getReadableName()
    {
        return $this->readableName;
    }

    /**
     * @param \Gladtur\TagBundle\Entity\UserLocationTagData $userLocationTagData
     */
    public function setUserLocationTagData($userLocationTagData)
    {
        $this->userLocationTagData = $userLocationTagData;
    }

    /**
     * @return \Gladtur\TagBundle\Entity\UserLocationTagData
     */
    public function getUserLocationTagData()
    {
        return $this->userLocationTagData;
    }


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
     * @var LocationCategory $locationTopCategory
     * @ORM\ManyToOne(targetEntity="LocationCategory", inversedBy="topCategoryLocation")
     **/
    private $locationTopCategory;

    /**
     * @param \Gladtur\TagBundle\Entity\LocationCategory $locationTopCategory
     */
    public function setTopCategory($locationTopCategory)
    {
        $this->locationTopCategory = $locationTopCategory;
    }

    /**
     * @return \Gladtur\TagBundle\Entity\LocationCategory
     */
    public function getTopCategory()
    {
        return $this->locationTopCategory;
    }

    /**
     * @var LocationCategory $locationCategories
     * @ORM\ManyToMany(targetEntity="LocationCategory", inversedBy="categoriesLocation")
     **/
    private $locationCategories;
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
    /**
     * Owning Side
     * @ORM\OneToMany(targetEntity="UserLocationData", mappedBy="location")
     * @ORM\OrderBy({"created_at" = "DESC"})
     * @var UserLocationData $userLocationData
     */
    private $userLocationData;

    /**
     * @return null
     */
    public function __construct()
    {
        $this->locationCategory = new \Doctrine\Common\Collections\ArrayCollection();
        $this->userLocationData = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setPublished(true);
    }

    /**
     * @return User
     */
    public function getUpdatedByUser()
    {
        return $this->updatedByUser;
    }

    /**
     * @param mixed $updatedByUser
     */
    public function setUpdatedByUser($updatedByUser)
    {
        $this->updatedByUser = $updatedByUser;
    }

    /**
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param string $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    // http://docs.doctrine-project.org/projects/doctrine-orm/en/2.0.x/reference/association-mapping.html#many-to-many-unidirectional

    /**
     * @param string $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
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
     * Get published
     *
     * @return boolean
     */
    public function getPublished()
    {
        return $this->published;
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

    public function mtGetPublishedTxt()
    {
        return ($this->published) ? 'Ja' : 'Nej';
    }

    public function mtGetAddressZip()
    {
        return ($this->getAddressZip()) ? $this->getAddressZip() : '----';
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
     * @return string
     */
    public function __toString()
    {
        $this->getUserLocationData()->getReadableName();
    }

    /**
     * @param bool $latestOnly
     * @return \Doctrine\Common\Collections\ArrayCollection|mixed
     */
    public function getUserLocationData($latestOnly = true)
    {
        if ($latestOnly) {
            return $this->userLocationData->first();
        }

        return $this->userLocationData;
    }

    public function setUserLocationData(UserLocationData $data)
    {
        $this->userLocationData = $data;

        return $this;
    }

    /**
     * @return Doctrine\Common\Collections\Collection
     */
    public function getLocationCategory()
    {
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

    /**
     * @return LocationCategory
     */
    public function getLocationCategories()
    {
        return $this->locationCategory;
    }

    /**
     * @return string
     */
    public function getAddressCountryReadable()
    {
        return 'Denmark';
    }

    /**
     * @param $name
     * @param null $arguments
     * @return mixed
     */
    public function __call($name, $arguments = null)
    {
        if (strpos($name, 'get') !== 0) {
            $name = 'get' . ucfirst($name);
        }

        if (!isset($this->locData)) {
            $this->locData = $this->getUserLocationData();
        }
        if (isset($this->locData) && $this->locData) {
            return $this->locData->$name($arguments);
        }

        return new UserLocationData();
    }
}