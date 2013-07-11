<?php

namespace Gladtur\TagBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\DateTimeType as ORMTYPE;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Gladtur\TagBundle\Entity\UserLocationTagData
 *
 * @ORM\Table(name="user_location_tag_data")
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
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

    public function getTag()
    {
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
     * @var integer $user
     * @ORM\ManyToOne(targetEntity="TvguserProfile", inversedBy="userLocationTagData")
     */
    private $user_profile;

    /**
     * @var integer $tagvalue
     * @ORM\Column(name="tagvalue", type="integer", nullable=true)
     */
    private $tagvalue;
    /**
     * @var boolean $relevant
     *
     * @ORM\Column(name="relevant", type="boolean", nullable=true)
     */
    private $relevant;


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
     * @param int $tagvalue
     */
    public function setTagvalue($tagvalue)
    {
        if(is_int($tagValue)){
            if(0 <= $tagValue && $tagValue <= 2){
                /**
                 * Enumerated value, range 0-2
                 * null/0 = Unrated
                 * 1 = Rated positively
                 * 2 = Rated negatively
                 */
                $this->tagvalue=$tagValue;
            }
        }
    }

    /**
     * @return int $tagvalue
     */
    public function getTagvalue()
    {
        if(!$this->tagvalue) return 0;
        return $this->tagvalue;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param int $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return int
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param int $user_profile
     */
    public function setUserProfile($user_profile)
    {
        $this->user_profile = $user_profile;
    }

    /**
     * @return int
     */
    public function getUserProfile()
    {
        return $this->user_profile;
    }

    /**
     * @param int $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return int
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param int $deletedAt
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * @return int
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
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