<?php

namespace Gladtur\TagBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Gladtur\TagBundle\Entity\LocationCategory
 *
 * @ORM\Table(name="location_category")
 * @ORM\Entity
 */
class LocationCategory
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
     * @ORM\Column(name="is_topcategory", type="boolean", nullable=true)
     */
    private $isTopcategory;

    /**
     * @ORM\Column(name="icon_path", type="string", nullable=true)
     */
    private $iconPath;

    private $iconUlDir; // Relative path (to "web/uploads" of the dir of the icon for each LocationCategory

    /**
     * @ORM\ManyToMany(targetEntity="Tag", mappedBy="location_categories")
     */
    private $tags;

    /**
     * @param mixed $isTopcategory
     */
    public function setIsTopcategory($isTopcategory)
    {
        $this->isTopcategory = $isTopcategory;
    }

    /**
     * @return mixed
     */
    public function getIsTopcategory()
    {
        return $this->isTopcategory;
    }


//http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html //
    /**
     * @ORM\OneToMany(targetEntity="LocationCategory", mappedBy="parentCategory")
     **/
    private $childCategories;
    /**
     * @ORM\ManyToOne(targetEntity="LocationCategory", inversedBy="childCategories")
     **/
    private $parentCategory;


    /**
     * @ORM\ManyToMany(targetEntity="Location", mappedBy="locationCategories")
     **/
    private $categoriesLocation;

    /**
     * @ORM\OneToMany(targetEntity="Location", mappedBy="location_top_category")
     **/
    private $topCategoryLocation;

    /**
     * @var string $readableName
     *
     * @ORM\Column(name="readable_name", type="string", length=255, nullable=true)
     */
    private $readableName;
    /**
     * @var boolean $published
     *
     * @ORM\Column(name="published", type="boolean", nullable=true)
     */
    private $published;

    public function __construct()
    {
        $this->childCategories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
        $this->iconUlDir = 'locationcategories/icons';
    }

    public function getChildCategories()
    {
        return $this->childCategories;
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

    public function mtGetPublishedTxt()
    {
        return ($this->published) ? 'Ja' : 'Nej';
    }

    public function __toString()
    {
        return $this->getReadableName();
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
     * Set readableName
     *
     * @param string $readableName
     * @return LocationCategory
     */
    public function setReadableName($readableName)
    {
        $this->readableName = $readableName;

        return $this;
    }

    public function getNestedReadableName()
    {
        if ($this->getParentCategory()) {
            return ' - ' . $this->getReadableName();
        }

        return $this->getReadableName();
    }

    public function getParentCategory()
    {
        return ($this->parentCategory) ? $this->parentCategory : null;
    }

    /**
     * Set parentCategory
     *
     * @param integer $parentCategory
     * @return LocationCategory
     */
    public function setParentCategory($parentCategory)
    {
        $this->parentCategory = $parentCategory;

        return $this;
    }

    public function getLocationCategoryUnassigned($categoryPublished = true)
    {

    }

    public function getName()
    {
        return $this->getReadableName();
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
        return $this->iconPath;
    }

    public function getIconUlDir()
    {
        return 'uploads/icons/locationcategories';
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

    /**
     * @return mixed
     */
    public function getCategoriesLocations()
    {
        return $this->categoriesLocation;
    }

    /**
     * @ORM\Column(name = "slug", type="string", length=255, nullable = true )
     */
    private $slug;

    public function getSlug()
    {
        return $this->slug;
    }

    public function slugify($srcString)
    {
        $srcString = str_replace(
            array('æ', 'ø', 'å', 'Æ', 'Ø', 'Å', ' ', '&', '.', ',', '--', '---'),
            array('ae', 'oe', 'aa', 'ae', 'oe', 'aa', '-', '-og-', '_', '_', '-', '-'),
            $srcString
        );
        $srcString = urlencode(strtolower($srcString));
        $this->slug = $srcString;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function addTags(ArrayCollection $tags)
    {
        $this->tags->add($tags);
    }

    public function removeTags(\Gladtur\TagBundle\Entity\Tag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }
}
