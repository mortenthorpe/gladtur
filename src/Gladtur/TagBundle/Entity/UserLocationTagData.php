<?php

namespace Gladtur\TagBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Gladtur\TagBundle\Entity\UserLocationTagData
 *
 * @ORM\Table(name="user_location_tag_data")
 * @ORM\Entity(repositoryClass="Gladtur\TagBundle\Entity\UserLocationTagDataRepository")
 * @ORM\HasLifecycleCallbacks
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
     * @ORM\ManyToOne(targetEntity="Tag", inversedBy="userLocationTagData")
     */
    private $tag;

    /**
     * @param mixed $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }


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
    public function setTagValue($tagvalue)
    {
        if (is_int($tagvalue)) {
            if (0 <= $tagvalue && $tagvalue <= 3) {
                /**
                 * Enumerated value, range 0-2
                 * null/0 = Unrated
                 * 1 = Rated positively
                 * 2 = Rated negatively
                 */
                $this->tagvalue = $tagvalue;
            }
        }
    }

    /**
     * @return int $tagvalue
     */
    public function getTagvalue()
    {
        if (!$this->tagvalue) {
            return 0;
        }

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

    /**
     * @var integer $score
     *
     * @ORM\Column(name="score", type="integer", nullable=true)
     */
    private $score;

    /**
     * @ORM\PrePersist
     */
    private function setScore($scoreVal)
    {
        $this->score = 1;
    }

    public function getScore()
    {
        return $this->score;
    }
}

class UserLocationTagDataRepository extends EntityRepository
{
    public function getLocationTagValue($tag_dataId, $location, $userProfileId)
    {
        return $this->_em->createQuery(
            "select tag_data.tagvalue from Gladtur\TagBundle\Entity\UserLocationTagData tag_data where tag_data.location = " . $location->getId(
            ) . " and tag_data.tag = " . $tag_dataId . " and tag_data.user_profile=" . $userProfileId . " order by tag_data.updated desc"
        )->getSingleScalarResult();
        //      return $this->_em->createQuery("select tag_data.tagvalue from Gladtur\TagBundle\Entity\UserLocationTagData tag_data where tag_data.location = ".$location->getId()." and tag_data.tag = ".$tag_dataId." and tag_data.user_profile in(select profile.id from Gladtur\TagBundle\Entity\TvguserProfile profile) order by tag_data.updated desc")->getArrayResult();
    }
}