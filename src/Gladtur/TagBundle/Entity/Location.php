<?php
namespace Gladtur\TagBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Solarium\Client;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Properties declared here are STATIC to the location, any mutable properties are declared inside the UserLocationData Class, as the mutability consists of changing users providing differing property-data for a specific location.
 * Static properties (the ones in this Class) have values assigned by the first user, or overridden by an ADMINISTRATOR type actor
 */

/**
 * Gladtur\TagBundle\Entity\Location
 *
 * @ORM\Table(name="location")
 * @ORM\Entity(repositoryClass="Gladtur\TagBundle\Entity\LocationRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class Location
{
    /*public function doStuffOnPrePersist()
    {
        $slugtxt = $this->readableName.'-'.$this->addressStreet;
        $this->slugify($slugtxt);
    }*/

    private $emptyString;
    /**
     * @ORM\OneToMany(targetEntity="UserLocationMedia", mappedBy="location")
     * @Assert\Valid
     */
    private $user_media;

    /**
     * @ORM\OneToMany(targetEntity="UserLocationTagData", mappedBy="location")
     * @var UserLocationTagData $userLocationTagData
     */
    private $userLocationTagData;


    /**
     * @ORM\OneToMany(targetEntity="UserLocationComments", mappedBy="location")
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $user_comments;

    /**
     * @var string $readableName
     *
     * @ORM\Column(name="readable_name", type="string", length=255, nullable=true)
     * @Assert\NotBlank(message = "Stedets navn skal være udfyldt")
     */
    private $readableName;

    /**
     * @ORM\Column(name="main_image_thumbnail", type="string", nullable=true)
     */
    private $mainImageThumbnail;
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
     * @var LocationCategory $location_top_category
     * @ORM\ManyToOne(targetEntity="LocationCategory", inversedBy="topCategoryLocation")
     * @Assert\NotBlank( message = "Stedet skal have 1 top kategori" )
     */
    private $location_top_category;
    /**
     * @var LocationCategory $locationCategories
     * @ORM\ManyToMany(targetEntity="LocationCategory", inversedBy="categoriesLocation")
     * @ORM\OrderBy({"readableName" = "ASC"})
     */
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
     * @Assert\Url(message = "Hjemmeside-adressen skal være en gyldig www-adresse")
     */
    private $homepage;
    /**
     * @var string $addressZip
     *
     * @ORM\Column(name="address_zip", type="string", length=20, nullable=true)
     * @Assert\NotBlank()
     */
    private $addressZip;
    /**
     * @var string $addressCountry
     *
     * @ORM\Column(name="address_country", type="string", length=255, nullable=true)
     */
    private $addressCountry = 'Danmark';
    /**
     * @var string $addressCity
     *
     * @ORM\Column(name="address_city", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $addressCity;
    /**
     * @var string $addressStreet
     *
     * @ORM\Column(name="address_street", type="string", length=255, nullable=true)
     * @Assert\NotBlank(message = "Gade/vej og husnummer skal være udfyldt")
     */
    private $addressStreet;
    /**
     * @var string $addressExtd
     *
     * @ORM\Column(name="address_extd", type="string", length=255, nullable=true)
     */
    private $addressExtd;
    /**
     * @var integer $osm_id
     * @ORM\Column(name="osm_id", type="integer", nullable=true)
     */
    private $osm_id;
    /**
     * @var integer $address_validated
     * @ORM\Column(name="address_validated", type="integer", nullable=true)
     */
    private $address_validated;
    /**
     * Owning Side
     * @ORM\OneToMany(targetEntity="UserLocationData", mappedBy="location", cascade={"persist"})
     * @ORM\OrderBy({"created_at" = "DESC"})
     * @var UserLocationData $userLocationData
     */
    private $userLocationData;
    /**
     * @var UserLocationData $userLocationDataLatest
     */
    private $userLocationDataLatest;
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="locations", fetch="EXTRA_LAZY")
     */
    private $created_by;
    /**
     * @ORM\Column(name = "slug", type="string", length=255, nullable = true )
     */
    private $slug;
    /**
     * @ORM\Column(name = "slug_no", type="integer", nullable = true )
     */
    private $slug_no;
    /**
     * @var integer $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="integer")
     */
    private $created;

    /**
     * @var string $phone
     *
     * @ORM\Column(name="phone", type="string", length=20, nullable=true)
     */
    /**
     * @var integer $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="integer")
     */
    private $updated;
    /**
     * @var integer $deletedAt
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private $deletedAt;
    /**
     * @var boolean $deleted
     */
    private $deleted;

    /**
     * @var integer $admin_validated
     * @ORM\Column(name="admin_validated", type="integer", nullable=true)
     */
    private $admin_validated;


    /**
     * @return null
     */
    public function __construct()
    {
        $this->locationCategories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->userLocationData = new \Doctrine\Common\Collections\ArrayCollection();
        $this->userLocationTagData = new \Doctrine\Common\Collections\ArrayCollection();
        $this->userLocationDataLatest = new UserLocationData();
        $this->user_media = new \Doctrine\Common\Collections\ArrayCollection();
        $this->user_comments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->updatedByUser = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setPublished(true);
        $this->score = 0;
        $this->emptyString = '- Ikke angivet -';
    }

    /**
     * @return \Gladtur\TagBundle\Entity\UserLocationTagData
     */
    public function getUserLocationTagData($latestOnly = true)
    {
        return $this->userLocationTagData;
    }

    /**
     * @param \Gladtur\TagBundle\Entity\UserLocationTagData $userLocationTagData
     */
    public function setUserLocationTagData($userLocationTagData)
    {
        $this->userLocationTagData = $userLocationTagData;
    }

    /**
     * @param \Gladtur\TagBundle\Entity\LocationCategory $locationTopCategory
     */
    public function setTopCategory($locationTopCategory)
    {
        $this->location_top_category = $locationTopCategory;
    }

    /**
     * @return \Gladtur\TagBundle\Entity\UserLocationData
     */
    public function getUserLocationDataLatest()
    {
        return $this->userLocationDataLatest;
    }

    /**
     * @param \Gladtur\TagBundle\Entity\UserLocationData $userLocationDataLatest
     */
    public function setUserLocationDataLatest($userLocationDataLatest)
    {
        $this->userLocationDataLatest = $userLocationDataLatest;

        /*$this->userLocationData->add($userLocationDataLatest);*/

        return;
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

    public function addUpdatedByUser($updatedByUser)
    {
        $this->updatedByUser->add($updatedByUser);
    }

    // http://docs.doctrine-project.org/projects/doctrine-orm/en/2.0.x/reference/association-mapping.html#many-to-many-unidirectional

    public function mtGetPublishedTxt()
    {
        return ($this->published) ? 'Ja' : 'Nej (Slettet)';
    }

    public function mtGetAddressZip()
    {
        return ($this->getAddressZip()) ? $this->getAddressZip() : '----';
    }

    /**
     * @return string
     */
    public function getAddressZip()
    {
        return ($this->addressZip) ? $this->addressZip : $this->emptyString;
    }

    /**
     * @param string $addressZip
     */
    public function setAddressZip($addressZip)
    {
        $this->addressZip = $addressZip;
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
     * Get homepage Readable without http-protocol
     *
     * @return string
     */
    public function getHomepageReadable()
    {
        if ($this->homepage) {
            $this->homepage = str_replace('http://', '', $this->homepage);

            return $this->homepage;
        } else {
            return $this->emptyString;
        }
    }

    public function getHomepageValid()
    {
        return ($this->homepage) ? true : false;
    }

    public function removeUserLocationData(UserLocationData $data)
    {
        $this->userLocationData->removeElement($data);
    }

    public function addUserLocationData(UserLocationData $data)
    {
        $this->userLocationData->add($data);

        return $this;
    }

    /**
     * @param LocationCategory $locationCategories
     * @return Location
     */
    public function addLocationCategory(\Gladtur\TagBundle\Entity\LocationCategory $locationCategories)
    {
        $this->locationCategories->add($locationCategories);
    }

    /*public function setUserLocationData(UserLocationData $data)
    {
        $this->userLocationData = $data;

        return $this;
    }
*/

    public function removeLocationCategory(\Gladtur\TagBundle\Entity\LocationCategory $locationCategories)
    {
        $this->locationCategories->removeElement($locationCategories);
    }

    /**
     * @param LocationCategory $locationCategories
     * @return Location
     */
    public function addLocationCategories(\Gladtur\TagBundle\Entity\LocationCategory $locationCategories)
    {
        $this->locationCategories->add($locationCategories);
    }

    public function removeLocationCategories(\Gladtur\TagBundle\Entity\LocationCategory $locationCategories)
    {
        $this->locationCategories->removeElement($locationCategories);
    }

    /**
     * @return string
     */
    public function getAddressCountryReadable()
    {
        return ($this->getAddressCountry()) ? $this->getAddressCountry() : $this->emptyString;
    }

    /**
     * @return string
     */
    public function getAddressCountry()
    {
        return ($this->addressCountry) ? $this->addressCountry : 'Danmark';
    }

    /**
     * @param string $addressCountry
     */
    public function setAddressCountry($addressCountry)
    {
        $this->addressCountry = ($addressCountry) ? $addressCountry : 'Danmark';
    }


    public function setScore($scoreInt)
    {
        $this->score = $scoreInt;
    }

    public function getScoreName()
    {
        if (!$this->getScore() || $this->getScore() == 3) {
            return 'unrated';
        }
        if ($this->getScore() == 1) return 'down';
        if ($this->getScore() == 2) return 'up';

        return 'neutral';
    }

    /**
     * @param $name
     * @param null $arguments
     * @return mixed
     */
    /*  public function __call($name, $arguments = null)
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
      }*/
    public function getScore()
    {
        return $this->score;
    }

    public function getMedia()
    {
        $mediaCollection = new ArrayCollection();
        /**
         * @var UserLocationMedia $media
         */
        foreach ($this->user_media as $media) {
            if (!$media->getIsmainimage()) $mediaCollection->add($media);
        }

        return $mediaCollection;
    }

    public function getMainImagePath()
    {
        'locations/' . $this->getId() . '/' . $this->getMainImage()->getMediaPath();
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

    public function getMainImage()
    {
        $defaultMedia = new UserLocationMedia('noimage.png');
        if ($this->user_media->isEmpty()) {
            return $defaultMedia;
        }
        /**
         * @var UserLocationMedia $media
         */
        foreach ($this->user_media as $media) {
            if ($media->getIsmainimage()) return $media;
        }

        return $defaultMedia;
    }

    /**
     * @param mixed $user_media
     */
    public function addUserMedia(UserLocationMedia $user_media)
    {
        $this->user_media->add($user_media);
    }

    /**
     * @return mixed
     */
    public function getUserMedia()
    {
        return $this->user_media;
    }

    /**
     * @return mixed
     */
    public function getUserComments()
    {
        return $this->user_comments;
    }

    /**
     * @param mixed $user_comments
     */
    public function setUserComments($user_comments)
    {
        $this->user_comments = $user_comments;
    }

    /**
     * @return \Gladtur\TagBundle\Entity\LocationCategory
     */
    public function getLocationTopCategory()
    {
        return $this->location_top_category;
    }

    /**
     * @param \Gladtur\TagBundle\Entity\LocationCategory $location_top_category
     */
    public function setLocationTopCategory($location_top_category)
    {
        $this->location_top_category = $location_top_category;
    }

    public function getAddressAssoc()
    {
        return array(
            'zip' => $this->getAddressZip(),
            'streetname' => $this->getAddressStreetAndExtd(),
            'city' => $this->getAddressCity(),
            'country' => $this->getAddressCountry()
        );
        //return array('zip'=>$this->getAddressZip(), 'streetname' => $this->getAddressStreet() . ' ' . $this->getAddressExtd(), 'city'=> $this->getAddressCity(), 'country'=>$this->getAddressCountry());
    }

    public function getAddressStreetAndExtd()
    {
        if (($this->getAddressValidated() && ($this->getAddressValidated() !== 0))) {
            return $this->getAddressStreet();
        } else {
            if ($this->getAddressExtd() && ($this->getAddressExtd() !== '') && (strpos(
                        strrev($this->getAddressStreet()),
                        strrev($this->getAddressExtd())
                    ) === 0)
            ) {
                return $this->getAddressStreet();
            } else {
                return $this->getAddressStreet() . ' ' . $this->getAddressExtd();
            }
        }
    }

    /**
     * @return int
     */
    public function getAddressValidated()
    {
        return $this->address_validated;
    }

    /**
     * @param int $address_validated
     */
    public function setAddressValidated($address_validated)
    {
        $this->address_validated = $address_validated;
    }

    /**
     * @return string
     */
    public function getAddressStreet()
    {
        return ($this->addressStreet) ? trim($this->addressStreet) : $this->emptyString;
    }

    /**
     * @param string $addressStreet
     */
    public function setAddressStreet($addressStreet)
    {
        $this->addressStreet = trim($addressStreet);
    }

    /**
     * @return string
     */
    public function getAddressExtd()
    {
        return ($this->addressExtd) ? $this->addressExtd : $this->emptyString;
    }

    /**
     * @param string $addressExtd
     */
    public function setAddressExtd($addressExtd)
    {
        $this->addressExtd = $addressExtd;
    }

    /**
     * @return string
     */
    public function getAddressCity()
    {
        return ($this->addressCity) ? $this->addressCity : $this->emptyString;
    }

    /**
     * @param string $addressCity
     */
    public function setAddressCity($addressCity)
    {
        $this->addressCity = $addressCity;
    }

    /**
     * @return mixed
     */
    public function getMainImageThumbnail($relPath = false)
    {
        /* if($this->mainImageThumbnail && $relPath){
             return '/uploads/avalanche/thumbnail/locations/'.$this->getId().'/'.$this->mainImageThumbnail;
         }*/
        return $this->mainImageThumbnail;
    }

    /**
     * @param mixed $mainImageThumbnail
     */
    public function setMainImageThumbnail($mainImageThumbnail)
    {
        $this->mainImageThumbnail = $mainImageThumbnail;
    }

    /**
     * @param Client $solariumClient
     * @return \Solarium\QueryType\Update\Query\Document\DocumentInterface
     * http://wiki.solarium-project.org/index.php/V3:Read-Write_document
     * Tips for SOLR http://wiki.apache.org/solr/SolrRelevancyFAQ
     */
    public function toSolariumDocument(Client $solariumClient)
    {
        $update = $solariumClient->createUpdate();
        $myDoc = $update->createDocument();
        $myDoc->id = $this->getId();
        if ($this->getReadableName() && $this->getPublished() && $this->getTopCategory() && $this->getSlug(
            ) && ($this->getSlug() !== '')
        ) {
            $myName = html_entity_decode($this->getReadableName());
            $myName = str_replace(
                array(
                    'Ö',
                    'Æ',
                    'Ø',
                    'Å',
                    'ö',
                    'æ',
                    'ø',
                    'å',
                    '\u00c6',
                    '\u00d8',
                    '\u00c5',
                    '\u00e6',
                    '\u00f8',
                    '\u00e5',
                    ' ',
                    '%'
                ),
                array(
                    'OE',
                    'AE',
                    'OE',
                    'AA',
                    'oe',
                    'ae',
                    'oe',
                    'aa',
                    'AE',
                    'OE',
                    'AA',
                    'ae',
                    'oe',
                    'aa',
                    '',
                    'pct'
                ),
                $myName
            );
            $myDoc->name = mb_strtolower($myName);
            $myDoc->topcategory_id = $this->getTopCategory()->getId();
            $topcategoryName = ($this->getTopCategory()->getReadableName() && ($this->getTopCategory()->getReadableName(
                    ) !== '')) ? $this->getTopCategory()->getReadableName() : '-invalid-';
            $myDoc->topcategory_name = mb_strtolower(
                str_replace(
                    array(
                        'Ö',
                        'Æ',
                        'Ø',
                        'Å',
                        'ö',
                        'æ',
                        'ø',
                        'å',
                        '\u00c6',
                        '\u00d8',
                        '\u00c5',
                        '\u00e6',
                        '\u00f8',
                        '\u00e5'
                    ),
                    array(
                        'OE',
                        'AE',
                        'OE',
                        'AA',
                        'oe',
                        'ae',
                        'oe',
                        'aa',
                        'AE',
                        'OE',
                        'AA',
                        'ae',
                        'oe',
                        'aa'
                    ),
                    html_entity_decode($topcategoryName)
                )
            );
            $myDoc->subcategory_ids = $this->getLocationCategoriesIds();
            $myDoc->subcategory_names = $this->getLocationCategoriesNames();
            $myDoc->search_aggregate = $this->getSearchAggregate();
            $myDoc->location = $this->getLatitude() . ',' . $this->getLongitude();
            $myDoc->location_lat_coordinate = $this->getLatitude();
            $myDoc->location_lon_coordinate = $this->getLongitude();
        }

        return $myDoc;
    }

    /**
     * @return string
     */
    public function getReadableName()
    {
        return str_replace('&#39;', '\'', html_entity_decode($this->readableName));
    }

    /**
     * @param string $readableName
     */
    public function setReadableName($readableName)
    {
        $this->readableName = $readableName;
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

    /**
     * @return \Gladtur\TagBundle\Entity\LocationCategory
     */
    public function getTopCategory()
    {
        return $this->location_top_category;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    private function getLocationCategoriesIds()
    {
        $idsArray = array();
        /**
         * @var LocationCategory $subcategory
         */
        foreach ($this->getLocationCategories() as $subcategory) {
            $idsArray[] = $subcategory->getId();
        }

        return $idsArray;
    }

    /**
     * @return LocationCategory
     */
    public function getLocationCategories()
    {
        return $this->locationCategories;
    }

    private function getLocationCategoriesNames()
    {
        $namesArray = array();
        /**
         * @var LocationCategory $subcategory
         */
        foreach ($this->getLocationCategories() as $subcategory) {
            $namesArray[] = mb_strtolower(
                str_replace(
                    array(
                        'Ö',
                        'Æ',
                        'Ø',
                        'Å',
                        'ö',
                        'æ',
                        'ø',
                        'å',
                        '\u00c6',
                        '\u00d8',
                        '\u00c5',
                        '\u00e6',
                        '\u00f8',
                        '\u00e5',
                        ' '
                    ),
                    array(
                        'OE',
                        'AE',
                        'OE',
                        'AA',
                        'oe',
                        'ae',
                        'oe',
                        'aa',
                        'AE',
                        'OE',
                        'AA',
                        'ae',
                        'oe',
                        'aa',
                        ''
                    ),
                    $subcategory->getReadableName()
                )
            );
        }

        return $namesArray;
    }

    public function getSearchAggregate()
    {
        $aggregateArr = array();
        $aggregateArr[] = mb_strtolower(
            str_replace(
                array(
                    'Ö',
                    'Æ',
                    'Ø',
                    'Å',
                    'ö',
                    'æ',
                    'ø',
                    'å',
                    '\u00c6',
                    '\u00d8',
                    '\u00c5',
                    '\u00e6',
                    '\u00f8',
                    '\u00e5',
                    ' ',
                    '%'
                ),
                array(
                    'OE',
                    'AE',
                    'OE',
                    'AA',
                    'oe',
                    'ae',
                    'oe',
                    'aa',
                    'AE',
                    'OE',
                    'AA',
                    'ae',
                    'oe',
                    'aa',
                    '',
                    'pct'
                ),
                html_entity_decode($this->getReadableName())
            )
        );
        $aggregateArr[] = mb_strtolower(
            str_replace(
                array(
                    'Ö',
                    'Æ',
                    'Ø',
                    'Å',
                    'ö',
                    'æ',
                    'ø',
                    'å',
                    '\u00c6',
                    '\u00d8',
                    '\u00c5',
                    '\u00e6',
                    '\u00f8',
                    '\u00e5',
                    ' '
                ),
                array(
                    'OE',
                    'AE',
                    'OE',
                    'AA',
                    'oe',
                    'ae',
                    'oe',
                    'aa',
                    'AE',
                    'OE',
                    'AA',
                    'ae',
                    'oe',
                    'aa',
                    ''
                ),
                html_entity_decode($this->getTopCategory()->getReadableName())
            )
        );

        return array_merge($aggregateArr, $this->getLocationCategoriesNames());
    }

    /**
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param string $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
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

    public function testEncoding()
    {
        $myName = str_replace(
            array('Ö', 'Æ', 'Ø', 'Å', 'ö', 'æ', 'ø', 'å', '\u00c6', '\u00d8', '\u00c5', '\u00e6', '\u00f8', '\u00e5'),
            array('OE', 'AE', 'OE', 'AA', 'oe', 'ae', 'oe', 'aa', 'AE', 'OE', 'AA', 'ae', 'oe', 'aa'),
            html_entity_decode($this->getReadableName())
        );
        file_put_contents('/symftemp/locationTitle.txt', $myName);
    }

    public function getStreetNameClean()
    {
        return str_replace(
            array('æ', 'ø', 'å', 'Æ', 'Ø', 'Å', '&', ' ', ','),
            array('ae', 'oe', 'aa', 'ae', 'oe', 'aa', '-og-', '-', '-'),
            $this->getAddressStreet()
        );
    }

    public function getNameClean()
    {
        return html_entity_decode(
            str_replace(
                array('æ', 'ø', 'å', 'Æ', 'Ø', 'Å', '&', ' ', ',', '%', '\''),
                array('ae', 'oe', 'aa', 'ae', 'oe', 'aa', '-og-', '-', '-', 'pct', ''),
                $this->getReadableName()
            )
        );
    }

    public function slugify($srcString)
    {
        $srcString = str_replace(
            array(
                '&#34;',
                '&#38;',
                '&#39;',
                'æ',
                'ø',
                'å',
                'Æ',
                'Ø',
                'Å',
                ' ',
                '&',
                '.',
                ',',
                '--',
                '---',
                '_-',
                '!',
                '--',
                'a/s',
                '/',
                '\\',
                '\'',
                '%',
                'é',
                '(',
                ')',
                '[',
                ']',
                '©',
                'ã',
                'ü',
                'Ü',
                'ä',
                'Ä',
                '–',
                '@',
                'è',
                'ö',
                'Ö',
                '´',
                'ô',
                'Ô',
                'ê',
                ':',
                ';',
                '+',
                'á',
                'À',
                '?',
                '`',
                '°',
                'ó',
                '–'
            ),
            array(
                '',
                '',
                '',
                'ae',
                'oe',
                'aa',
                'ae',
                'oe',
                'aa',
                '-',
                '-og-',
                '_',
                '_',
                '-',
                '-',
                '_',
                '',
                '-',
                '-as',
                '_',
                '_',
                '',
                'pct',
                'e',
                '',
                '',
                '',
                '',
                'c',
                'a',
                'y',
                'Y',
                'ae',
                'Ae',
                '-',
                'at',
                'e',
                'oe',
                'Oe',
                '',
                'o',
                'O',
                'e',
                '-',
                '',
                '-',
                'a',
                'A',
                '',
                '',
                'grader',
                'o',
                '-'
            ),
            $srcString
        );
        $srcString = urlencode(substr(mb_strtolower($srcString), 0, 254));
        $srcString = preg_replace('/\%\w\w/i', '', $srcString);
        $this->setSlug(trim($srcString));
    }

    /**
     * @return int
     */
    public function getOsmId()
    {
        return $this->osm_id;
    }

    /**
     * @param int $osm_id
     */
    public function setOsmId($osm_id)
    {
        $this->osm_id = $osm_id;
    }

    /**
     * @return integer
     */
    public function getDeletedAt($raw = true)
    {
        if ($raw) return $this->deletedAt;

        return strtotime($this->deletedAt);
    }

    public function setDeletedAt($isDeleted)
    {
        if (!$isDeleted) {
            $this->deletedAt = null;
        }
    }

    /**
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->isDeleted();
    }

    public function setDeleted($deleted)
    {
        if ($deleted) {
            $this->deletedAt = new \DateTime();
        } else {
            $this->deletedAt = null;
        }
        $this->setEnabled(false);

        return $this;
    }

    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return ($this->deletedAt) ? true : false;
    }

    /** Temporary, delete/modify for real version! */
    public function setOpeninghours($hourstxt = '')
    {
        return $this;
    }

    public function getOpeninghours()
    {
        return $this->getUserLocationData()->getDaysHoursOpenClosed();
    }

    /**
     * @param bool $latestOnly
     * @return \Doctrine\Common\Collections\ArrayCollection|mixed
     */
    public function getUserLocationData($latestOnly = false)
    {
        if (!$this->userLocationData) $this->userLocationData = new ArrayCollection();
        if ($latestOnly) {
            return $this->userLocationData->first();
        }

        return $this->userLocationData;
    }

    public function setUserLocationData($locdata)
    {
        if (!$locdata) $locdata = new UserLocationData();
        $this->userLocationData = $locdata;
    }

    /** ./Temporary, delete/modify for real version! */

    public function getAddressIsSet()
    {
        return (($this->getAddressStreet() !== $this->emptyString) && ($this->getAddressCity(
                ) !== $this->emptyString) && ($this->getAddressZip() !== $this->emptyString)) ? true : false;
    }

    public function getFullAddress()
    {
        return $this->getAddressStreet() . ', ' . $this->getAddressZip() . ' ' . $this->getAddressCity(
        ) . ', ' . $this->getCountry();
    }

    public function getCountry()
    {
        return 'Denmark';
    }

    public function setAddressCityAndZip($zip)
    {
        $oioxml_json_string = file_get_contents('postnumre.json');
        $postalCodesAndcitiesJSONAssoc = json_decode($oioxml_json_string, true);
        $postalCodesAndcitiesAssoc = array();
        foreach ($postalCodesAndcitiesJSONAssoc as $json_row) {
            $postalCitiesAssoc[$json_row['nr']] = $json_row['navn'];
        }
        $this->setAddressCity($postalCitiesAssoc[$zip]);
        $this->setAddressZip($zip);
    }

    public function getAddressCityAndZip()
    {
        return $this->getAddressCity() . ' ( ' . $this->getAddressZip() . ' )';
    }

    public function getAddressStreetAndExtdClean()
    {
        return html_entity_decode(
            str_replace(
                array('æ', 'ø', 'å', 'Æ', 'Ø', 'Å', '&', ' ', ',', '%', '\''),
                array('ae', 'oe', 'aa', 'ae', 'oe', 'aa', '-og-', '-', '-', 'pct', ''),
                $this->getAddressStreetAndExtd()
            )
        );
    }

    public function setAddressStreetAndExtd($addressStreetAndExtd)
    {
        $this->setAddressStreet(trim($addressStreetAndExtd));
        $this->setAddressValidated(true);
    }

    /**
     * @return mixed
     */
    public function getSlugNo()
    {
        return $this->slug_no;
    }

    /**
     * @param mixed $slug_no
     */
    public function setSlugNo($slug_no)
    {
        $this->slug_no = $slug_no;
    }

    /**
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * @param mixed $created_by
     */
    public function setCreatedBy(User $created_by)
    {
        $this->created_by = $created_by;
    }

    /**
     * @param int $admin_validated
     */
    public function setAdminValidated($admin_validated)
    {
        $this->admin_validated = $admin_validated;
    }

    /**
     * @return int
     */
    public function getAdminValidated()
    {
        return $this->admin_validated;
    }

    public function getAdminValidatedBoolean(){
        return($this->admin_validated)?true:false;
    }

    public function setAdminValidatedBoolean($admin_validated_boolean){
        if($admin_validated_boolean){
            $this->setAdminValidated(time());
        }
        else{
            $this->setAdminValidated(null);
        }
    }
}