<?php
// src/Gladtur/UserBundle/Entity/User.php

namespace Gladtur\TagBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OneToOne(targetEntity="UserLocationTagData",inversedBy="user", fetch="EXTRA_LAZY")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="UserLocationData", mappedBy="user", fetch="EXTRA_LAZY")
     */
    protected $userLocationData;


    /**
     * @ORM\OneToMany(targetEntity="UserLocationTagData", mappedBy="user", fetch="EXTRA_LAZY")
     */
    protected $userLocationTagData;


    /**
     * @ORM\OneToMany(targetEntity="UserLocationMedia", mappedBy="user")
     */
    private $user_media;

    /**
     * @ORM\OneToMany(targetEntity="UserLocationComments", mappedBy="user")
     */
    private $location_comments;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TvguserProfile", inversedBy="user", fetch="EXTRA_LAZY" )
     */
    protected $profile;

    /**
     *
     * @ORM\OneToOne(targetEntity="UserPassword", mappedBy="user")
     */
    protected $newpassword;

    /**
     * @var boolean $newsletter
     * @ORM\Column(name="newsletter", type="boolean", nullable=true)
     */
    private $newsletter;

    /**
     * @ORM\OneToOne(targetEntity="UserProfileByTags", inversedBy="user", cascade={"persist"})
     */
    protected $freeProfile;

    /**
     * @var string $latitude
     *
     * @ORM\Column(name="latitude", type="string", length=64, nullable=true)
     **/
    private $latitude;

    /**
     * @var string $longitude
     *
     * @ORM\Column(name="longitude", type="string", length=64, nullable=true)
     **/
    private $longitude;

    /**
     * @ORM\OneToMany(targetEntity="Location", mappedBy="created_by", fetch="EXTRA_LAZY")
     */
    private $locations;

    /**
     * @param mixed $freeProfile
     */
    public function setFreeProfile($freeProfile)
    {
        $this->freeProfile = $freeProfile;
    }

    /**
     * @return mixed
     */
    public function getFreeProfile()
    {
        return $this->freeProfile;
    }


    /**
     * @param mixed $newsletter
     * @return User
     */
    public function setNewsletter($newsletter)
    {
        $this->newsletter = $newsletter;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }

    /**
     * @var boolean $contact
     * @ORM\Column(name="contact", type="boolean", nullable=true)
     */
    private $contact;

    /**
     * @param boolean $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @var integer $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="integer")
     */
    private $created;

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
     * @return integer
     */
    public function getDeletedAt($raw = true)
    {
        if ($raw) {
            return $this->deletedAt;
        }

        return strtotime($this->deletedAt);
    }

    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return ($this->deletedAt) ? true : false;
    }

    /**
     * @var boolean $deleted
     */
    private $deleted;

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

    public function setDeletedAt($isDeleted)
    {
        if (!$isDeleted) {
            $this->deletedAt = null;
        }
    }

    public function setProfile(TvguserProfile $profile)
    {
        $this->profile = $profile;
    }

    public function getProfile()
    {
        return $this->profile;
    }

    public function __construct()
    {
        $this->locations = new ArrayCollection();
        parent::__construct();
        // your own logic
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
     * Set profileId
     *
     * @param integer $profileId
     * @return User
     */
    public function setProfileId($profileId)
    {
        $this->profileId = $profileId;

        return $this;
    }

    /**
     * Get profileId
     *
     * @return integer
     */
    public function getProfileId()
    {
        //return parent::getEmail();
        return $this->profileId;
    }

    public function getUsername()
    {
        return parent::getUsername();
    }

    public function __toString()
    {
        return parent::getUsername(); //$this->getId();
    }

    public function getUserNameForUid($user_id)
    {
        $userManager = $this->get('fos_user.user_manager');

        return $userManager->findBy(array('id' => 1))->getUsername();
    }

    public function getUniqeReference()
    {
        return $this->username . '(' . $this->getId() . ')';
    }

    /**
     * @param mixed $newpassword
     */
    public function setNewpassword($newpassword)
    {
        $this->newpassword = $newpassword;
    }

    /**
     * @return mixed
     */
    public function getNewpassword()
    {
        return $this->newpassword;
    }

    /**
     * @ORM\OneToMany(targetEntity="ReportedItem", mappedBy="user")
     */
    private $report_items;

    /**
     * @ORM\OneToMany(targetEntity="EventLogger", mappedBy="user")
     */
    private $events;

    public function getTags()
    {
        if ($this->getProfile()) {
            return $this->getProfile()->getTags();
        } else {
            return new ArrayCollection();
        }
    }

    public function addTags(\Gladtur\TagBundle\Entity\Tag $tags)
    {
        $this->getProfile()->addTags($tags);

        return $this;
    }


    public function removeTags(\Gladtur\TagBundle\Entity\Tag $tag)
    {
        $this->getProfile()->getTags()->removeElement($tag);
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setTags()
    {
        return;
    }

    public function getIndividualized()
    {
        return ($this->getFreeProfile()) ? true : false;
    }

    public function setIndividualized()
    {
        return;
    }

    public function getFreeprofileTags()
    {
        if ($this->getFreeProfile() && $this->getFreeProfile()->getProfileActive()) {
            return $this->getFreeProfile()->getProfileTags();
        } else return new ArrayCollection();
    }

    public function addFreeprofileTags(\Gladtur\TagBundle\Entity\Tag $tag)
    {
        if ($this->getFreeProfile() && $this->getFreeProfile()->getProfileActive()) {
            $this->getFreeProfile()->addProfileTag($tag);
        }
    }

    public function setFreeprofileTags(ArrayCollection $tags)
    {
        $freeProfile = $this->freeProfile;
        if (!$freeProfile) {
            $freeProfile = new UserProfileByTags();
            $this->setFreeProfile($freeProfile);
        } else {
            $freeProfile = $this->getFreeProfile();
        }
        $freeProfile->setProfileActive(true);
        $freeProfile->setProfileTags($tags);
    }

    public function hasRole($role)
    {
        if (in_array($role, $this->getRoles())) return true;

        return false;
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
    public function hasLatitude()
    {
        return (isset($this->latitude) && ($this->latitude <> '')) ? true : false;
    }

    public function getLatitude()
    {
        return $this->latitude;
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
    public function hasLongitude()
    {
        return (isset($this->longitude) && ($this->longitude <> '')) ? true : false;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param mixed $locations
     */
    public function addLocations($locations)
    {
        $this->locations->add($locations);
    }

    /**
     * @return mixed
     */
    public function getLocations()
    {
        return $this->locations;
    }
    // Login Listener example: http://blog.logicexception.com/2011/11/adding-post-login-logic-to.html
}