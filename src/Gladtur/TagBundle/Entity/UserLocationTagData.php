<?php

namespace Gladtur\TagBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Gladtur\TagBundle\Entity\UserLocationTagData
 *
 * @ORM\Table(name="user_location_tag_data")
 * @ORM\Entity
 */
class UserLocationTagData
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
     * @ORM\OneToOne(targetEntity="Tag", inversedBy="userLocationTagData")
     */
    private $tag;

    public function getTag(){
        return $this->tag;
    }
    /**
     * @ORM\ManyToOne(targetEntity="Location", inversedBy="userLocationTagData")
     */
    protected $location;
    /**
    * @var integer $user
    * @ORM\ManyToOne(targetEntity="User", inversedBy="userLocationTagData")
    */
    private $user;

    /**
     * @var boolean $relevant
     *
     * @ORM\Column(name="relevant", type="boolean", nullable=true)
     */
    private $relevant;
	/**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    public function setUser($user)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set relevant
     *
     * @param boolean $relevant
     * @return UserLocationTagData
     */
    public function setRelevant($relevant)
    {
        $this->relevant = $relevant;
    
        return $this;
    }

    /**
     * Get relevant
     *
     * @return boolean 
     */
    public function getRelevant()
    {
        return $this->relevant;
    }
}