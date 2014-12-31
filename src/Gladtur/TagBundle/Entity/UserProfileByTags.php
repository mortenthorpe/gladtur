<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 9/18/13
 * Time: 11:21 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Gladtur\TagBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="userprofile_by_tags")
 */
class UserProfileByTags
{

    public function __construct()
    {
        $this->profileTags = new ArrayCollection();
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="TvguserProfile", inversedBy="freeProfile")
     */
    protected $userProfile;

    /**
     * @ORM\OneToOne(targetEntity="User", mappedBy="freeProfile")
     */
    protected $user;

    /**
     * OneToMany, as defined by a ManyToMany with a uniqueness constraint and the red. JoinTable
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="freeProfiles")
     */
    protected $profileTags;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $profileActive;

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $profileActive
     */
    public function setProfileActive($profileActive)
    {
        $this->profileActive = $profileActive;
    }

    /**
     * @return mixed
     */
    public function getProfileActive()
    {
        return $this->profileActive;
    }

    /**
     * @param mixed $profileTag
     */
    public function addProfileTag($profileTag)
    {
        $this->profileTags->add($profileTag);

        return $this;
    }

    public function removeProfileTag($profileTag)
    {
        $this->profileTags->removeElement($profileTag);

        return $this;
    }

    public function removeProfileTags()
    {
        foreach ($this->profileTags as $tag) {
            $this->profileTags->removeElement($tag);
        }

        return $this;
    }

    public function setProfileTags($profileTags)
    {
        $this->profileTags = $profileTags;
    }

    /**
     * @return mixed
     */
    public function getProfileTags($locationCategoryId = null)
    {
        if (!$locationCategoryId) {
            return $this->profileTags;
        } else {
            $categoryTags = new ArrayCollection();
            foreach ($this->profileTags as $tag) {
                $locationCategoryIds = array();
                foreach ($tag->getLocationCategories() as $tagLocationCategory) {
                    $locationCategoryIds[] = $tagLocationCategory->getId();
                }
                if (in_array($locationCategoryId, $locationCategoryIds)) {
                    $categoryTags->add($tag);
                }
            }
        }

        return $categoryTags;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

}