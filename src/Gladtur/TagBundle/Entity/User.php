<?php
// src/Gladtur/UserBundle/Entity/User.php

namespace Gladtur\TagBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\DateTimeType as ORMTYPE;
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
     * @ORM\ManyToMany(targetEntity="Location", mappedBy="updatedByUser", fetch="EXTRA_LAZY")
     */
    private $location;

    /**
     * @ORM\OneToMany(targetEntity="UserLocationData", mappedBy="user", fetch="EXTRA_LAZY")
     */
    protected $userLocationData;


    /**
     * @ORM\OneToMany(targetEntity="UserLocationTagData", mappedBy="user", fetch="EXTRA_LAZY")
     */
    protected $userLocationTagData;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TvguserProfile", inversedBy="user", fetch="EXTRA_LAZY")
     */
    protected $profile;

    /**
     *
     * @ORM\OneToOne(targetEntity="UserPassword", mappedBy="user")
     */
    protected $newpassword;


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
    public function getDeletedAt($raw=true){
        if($raw) return $this->deletedAt;
        return strtotime($this->deletedAt);
    }
    /**
     * @return boolean
     */
    public function isDeleted(){
        return ($this->deletedAt)?true:false;
    }

    public function setDeletedAt($isDeleted){
        if(!$isDeleted){
            $this->deletedAt = null;
        }
    }

    public function setProfile(TvguserProfile $profile)
    {
        $this->profile = $profile;

        return $this;
    }

    public function getProfile()
    {
        return $this->profile;
    }

    public function __construct()
    {
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

}