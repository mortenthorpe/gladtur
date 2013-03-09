<?php
// src/Gladtur/UserBundle/Entity/User.php

namespace Gladtur\TagBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OneToOne(targetEntity="UserLocationTagData",inversedBy="user")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="LocationCategory", mappedBy="user")
     **/
    private $locationCategory;

    /**
    * @ORM\OneToMany(targetEntity="UserLocationData", mappedBy="user")
    */
    protected $userLocationData;

    /**
     * @ORM\OneToMany(targetEntity="UserLocationTagData", mappedBy="user")
     */
    protected $userLocationTagData;

    /**
     *
     * @ORM\OneToOne(targetEntity="TvguserProfile", mappedBy="user")
     */
    protected $profile;
    
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
        return $this->profileId;
    }

    public function getUsername(){
        return parent::getUsername();
    }

    public function __toString(){
       return parent::getUsername();//$this->getId();
    }

    public function getUserNameForUid($user_id){
        $userManager=$this->get('fos_user.user_manager');
        return $userManager->findBy(array('id'=>1))->getUsername();
    }
}