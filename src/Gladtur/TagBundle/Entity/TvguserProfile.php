<?php
namespace Gladtur\TagBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Gladtur\TagBundle\Entity\TvguserProfileRepository")
 * @ORM\Table(name="tvguser_profile")
 */
class TvguserProfile
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="profiles")
     */
    private $tags;

    /**
     * @ORM\OneToOne(targetEntity="UserProfileByTags", mappedBy="userProfile")
     */
    protected $freeProfile;

    /**
     * @ORM\Column(name="individualized", type="boolean", nullable=false)
     */
    protected $individualized;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $webiconpath;

    /**
     * @param mixed $webiconpath
     */
    public function setWebiconpath($webiconpath)
    {
        $this->webiconpath = $webiconpath;
    }

    /**
     * @return mixed
     */
    public function getWebiconpath()
    {
        return $this->webiconpath;
    }

    /**
     * @var integer $user
     * @ORM\OneToMany(targetEntity="UserLocationTagData", mappedBy="user_profile")
     */
    private $userLocationTagData;
    /**
     *
     * @ORM\OneToMany(targetEntity="User", mappedBy="profile")
     */
    protected $user;

    /**
     * @ORM\OneToMany(targetEntity="UserLocationComments", mappedBy="profile")
     */
    private $user_location_comments;

    /**
     * @ORM\OneToMany(targetEntity="UserLocationData", mappedBy="profile")
     */
    private $user_location_data;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rank;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $txt_description;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    /**
     * @var string $readableName
     *
     * @ORM\Column(name="readable_name", type="string", length=255, nullable=true)
     */
    private $readableName;

    public function getFullName()
    {
        return $this->getReadableName();
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
     * @Assert\File(maxSize="5M")
     */
    private $avatar;

    /**
     * @Assert\File(maxSize="5M")
     */
    private $webavatar;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;

    public function addTags(\Gladtur\TagBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;

        return $this;
    }

    public function removeTags(\Gladtur\TagBundle\Entity\Tag $tag)
    {
        $this->tags->removeElement($tag);
    }

    public function getTags($locationCategoryId = null)
    {
        if (!$locationCategoryId) {
            return $this->tags;
        } else {
            $categoryTags = new ArrayCollection();
            foreach ($this->tags as $tag) {
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


    public function getReadableName()
    {
        return $this->readableName;
    }

    public function setReadableName($readableName)
    {
        $this->readableName = $readableName;

        return $this;
    }

    /**
     * Get avatar.
     *
     * @return UploadedFile
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Sets avatar.
     *
     * @param UploadedFile $avatar
     */
    public function setAvatar(UploadedFile $avatar = null)
    {
        $this->avatar = $avatar;
    }

    /**
     * @param UploadedFile $webavatar
     */
    public function setWebavatar(UploadedFile $webavatar = null)
    {
        $this->webavatar = $webavatar;
    }

    /**
     * @return UploadedFile
     */
    public function getWebavatar()
    {
        return $this->webavatar;
    }


    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir() . '/' . $this->path;
    }

    /** Gets the path for the App-icon for use in Form Builders **/
    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir() . '/' . $this->path;
    }

    /** Gets the path for the Website-icon for use in Form Builders **/
    public function getWebPathSite()
    {
        return null === $this->webiconpath
            ? null
            : $this->getUploadDir() . '/website/' . $this->webiconpath;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/icons/profiles';
    }

    public function upload($profileForm, $fieldname = null)
    {
        if (!$fieldname) {
            return;
        } // Nofilefield used asa source
        /*
     *
            // the file property can be empty if the field is not required
            if (null === $this->getAvatar()) {
                return;
            }
    */
        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        $ulRootDir = $this->getUploadRootDir();
        if ($fieldname == 'webavatar') {
            $ulRootDir = $this->getUploadRootDir() . '/website';
        }
        if ($profileForm[$fieldname]->getData()) {
            $avalancheDir = str_replace('/web/uploads/', '/web/uploads/avalanche/thumbnail/', $ulRootDir);
            if (file_exists($avalancheDir . '/' . $profileForm[$fieldname]->getData()->getClientOriginalName())) {
                unlink($avalancheDir . '/' . $profileForm[$fieldname]->getData()->getClientOriginalName());
            }
            $profileForm[$fieldname]->getData()->move(
            // $this->getAvatar()->move(
                $ulRootDir,
                $profileForm[$fieldname]->getData()->getClientOriginalName()
            );
            if ($fieldname == 'webavatar') {
                // Icon path for the website icon
                $this->webiconpath = $profileForm[$fieldname]->getData()->getClientOriginalName();
            } else {
                // Icon path for the App icon
                $this->path = $profileForm[$fieldname]->getData()->getClientOriginalName();
            }

            // clean up the file property as you won't need it anymore
            // $this->avatar = null;
        }
    }

    public function asJSON()
    {
        return array($this->getId(), $this->getReadableName());
    }

    public function __toString()
    {
        return $this->getReadableName();
    }

    /**
     * @param mixed $rank
     */
    public function setRank($rank)
    {
        $this->rank = $rank;
    }

    /**
     * @return mixed
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * @param mixed $txt_description
     */
    public function setTxtDescription($txt_description)
    {
        $this->txt_description = $txt_description;
    }

    /**
     * @return mixed
     */
    public function getTxtDescription()
    {
        return $this->txt_description;
    }

    /**
     * @ORM\OneToMany(targetEntity="ReportedItem", mappedBy="user_profile")
     */
    private $report_items;

    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param bool $individualized
     */
    public function setIndividualized($individualized)
    {
        $this->individualized = $individualized;
    }

    /**
     * @return bool
     */
    public function getIndividualized()
    {
        return $this->individualized;
    }

}
