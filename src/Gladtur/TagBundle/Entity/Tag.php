<?php

namespace Gladtur\TagBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Gladtur\TagBundle\Entity\Tag
 *
 * @ORM\Table(name="tag")
 * @ORM\Entity
 */
class Tag
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
     * @var boolean $published
     *
     * @ORM\Column(name="published", type="boolean", nullable=true)
     */
    private $published;

    /**
     * @var string $readableName
     *
     * @ORM\Column(name="readable_name", type="string", length=255, nullable=true)
     */
    private $readableName;

    /**
     * @var string $textDescription
     *
     * @ORM\Column(name="text_description", type="text", nullable=true)
     */
    private $textDescription;

    /**
     * @var TagCategory
     *
     * @ORM\ManyToOne(targetEntity="TagCategory", inversedBy="tags")
     */
    private $tagCategory;

    /**
     * @ORM\ManyToMany(targetEntity="TvguserProfile", mappedBy="tags")
     */
    private $profiles;


    /**
     * @ORM\ManyToMany(targetEntity="LocationCategory", inversedBy="tags")
     */
    private $location_categories;

    /**
     * @ORM\ManyToMany(targetEntity="UserProfileByTags", mappedBy="profileTags")
     */
    private $freeProfiles;

    /**
     * @ORM\Column(name="icon_path", type="string", nullable=true)
     */
    private $iconPath;

    private $iconUlDir; // Relative path (to "web/uploads" of the dir of the icon for each tag

    /**
     * @Assert\File(maxSize = "5M")
     */
    private $iconVirtual;

    /**
     * @param mixed $iconVirtual
     */
    public function setIconVirtual($iconVirtual)
    {
        $this->iconVirtual = $iconVirtual;
    }

    /**
     * @return mixed
     */
    public function getIconVirtual()
    {
        return $this->iconVirtual;
    }


    public function addProfiles(\Gladtur\TagBundle\Entity\TvguserProfile $profile)
    {
        $this->profiles[] = $profile;
        $profile->addTags($this);

        return $this;
    }

    public function removeProfiles(\Gladtur\TagBundle\Entity\TvguserProfile $profile)
    {
        $this->profiles->removeElement($profile);
        $profile->removeTags($this);
    }

    /**
     * @var integer $user
     * @ORM\OneToMany(targetEntity="UserLocationTagData", mappedBy="tag")
     */
    protected $userLocationTagData;

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
     * Set published
     *
     * @param boolean $published
     * @return Tag
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
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

    public function mtGetPublishedTxt()
    {
        return ($this->published) ? 'Ja' : 'Nej';
    }

    /**
     * Set readableName
     *
     * @param string $readableName
     * @return Tag
     */
    public function setReadableName($readableName)
    {
        $this->readableName = $readableName;

        return $this;
    }

    /**
     * Get readableName
     *
     * @return string
     */
    public function getReadableName()
    {
        return $this->readableName;
    }

    /**
     * Set textDescription
     *
     * @param string $textDescription
     * @return Tag
     */
    public function setTextDescription($textDescription)
    {
        $this->textDescription = $textDescription;

        return $this;
    }

    /**
     * Get textDescription
     *
     * @return string
     */
    public function getTextDescription()
    {
        return $this->textDescription;
    }

    /**
     * Set tagCategory
     *
     * @param Gladtur\TagBundle\Entity\TagCategory $tagCategory
     * @return Tag
     */
    public function setTagCategory(\Gladtur\TagBundle\Entity\TagCategory $tagCategory = null)
    {
        $this->tagCategory = $tagCategory;

        return $this;
    }

    /**
     * Get tagCategory
     *
     * @return Gladtur\TagBundle\Entity\TagCategory
     */
    public function getTagCategory()
    {
        return $this->tagCategory;
    }

    /**
     * Set profilesRelevance
     *
     * @param array profilesRelevance
     * @return Tag
     */
    public function setProfilesRelevance($profilesRelevance)
    {
        $this->profilesRelevance = $profilesRelevance;

        return $this;
    }

    /**
     * Get profilesRelevance
     *
     * @return array
     */
    public function getRelevance()
    {
        return $this->Relevance;
    }

    public function __toString()
    {
        return $this->getReadableName();
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->profiles = new ArrayCollection();
        $this->location_categories = new ArrayCollection();
        $this->iconUlDir = 'icons/tags';
    }

    /**
     * Get profiles
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getProfiles()
    {
        return $this->profiles;
    }

    /**
     * @param mixed $iconPath
     */
    public function setIconPath($iconPath)
    {
        $this->iconPath = $iconPath;
    }

    /**
     * @return mixed
     */
    public function getIconPath()
    {
        if (!$this->iconPath) {
            return null;
        }

        return 'icons/tags/' . $this->iconPath;
    }

    public function getIconPathRaw()
    {
        return $this->iconPath;
    }

    /**
     * @return string
     */
    public function getIconUlDir()
    {
        return 'uploads/icons/tags';
    }


    public function getAbsolutePath()
    {
        return null === $this->iconPath
            ? null
            : $this->getUploadRootDir() . '/' . $this->iconPath;
    }

    public function getWebPath()
    {
        return null === $this->iconPath
            ? null
            : $this->getIconUlDir() . '/' . $this->iconPath;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../../web/' . $this->getIconUlDir();
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
        if ($profileForm[$fieldname]->getData()) {
            $profileForm[$fieldname]->getData()->move(
            // $this->getAvatar()->move(
                $this->getUploadRootDir(),
                $profileForm[$fieldname]->getData()->getClientOriginalName()
            );

            // set the path property to the filename where you've saved the file
            $this->iconPath = $profileForm[$fieldname]->getData()->getClientOriginalName();

            // clean up the file property as you won't need it anymore
            // $this->avatar = null;
        }
    }

    public function getPath()
    {
        return ($this->iconPath) ? $this->getWebPath() : basename($this->getWebPath()) . 'icons/tags/empty.png';
    }

    public function addLocationCategories(ArrayCollection $location_categories)
    {
        $this->location_categories->add($location_categories);
    }

    public function removeLocationCategories(\Gladtur\TagBundle\Entity\LocationCategory $location_category)
    {
        $this->location_categories->removeElement($location_category);
    }

    /**
     * @return mixed
     */
    public function getLocationCategories()
    {
        return $this->location_categories;
    }

}