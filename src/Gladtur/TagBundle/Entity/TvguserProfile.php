<?php
namespace Gladtur\TagBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
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
//http://tracehello.wordpress.com/2011/05/08/symfony2-doctrine2-manytomany-association/
// http://docs.doctrine-project.org/projects/doctrine-orm/en/2.0.x/reference/association-mapping.html#many-to-many-unidirectional
/**
 * Owning Side
 *
 * @ORM\ManyToMany(targetEntity="TagCategory", inversedBy="profiles")
 * @ORM\JoinTable(name="tvguser_profile_tagcategories")
 */
    private $tagCategories;


    /**
     *
     * @ORM\OneToMany(targetEntity="User", mappedBy="profile")
     */
    protected $user;

    public function __construct()
    {
    
    }

    /**
     * @var string $readableName
     *
     * @ORM\Column(name="readable_name", type="string", length=255, nullable=true)
     */
    private $readableName;

    public function getFullName(){
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
     * @Assert\File(maxSize="6000000")
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * Add tagCategories
     *
     * @param Gladtur\TagBundle\Entity\TagCategory $tagCategories
     * @return TvguserProfile
     */
    public function addTagcategory(\Gladtur\TagBundle\Entity\TagCategory $tagCategories)
    {
        $this->tagCategories[] = $tagCategories;
    
        return $this;
    }

    /**
     * Remove tags
     *
     * @param Gladtur\TagBundle\Entity\Tag $tags
     */
    public function removeTagcategory(\Gladtur\TagBundle\Entity\TagCategory $tagCategories)
    {
        $this->tagCategories->removeElement($tagCategories);
    }

    /**
     * Get tags
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getTagcategories()
    {
        return $this->tagCategories;
    }
    
    public function getReadableName(){
        return $this->readableName;
    }
    
    public function setReadableName($readableName){
        $this->readableName=$readableName;
        return $this;
    }

    /**
     * Get avatar.
     *
     * @return UploadedFile
     */
    public function getAvatar(){
        return $this->avatar;
    }

    /**
     * Sets avatar.
     *
     * @param UploadedFile $avatar
     */
    public function setAvatar(UploadedFile $avatar = null){
        $this->avatar = $avatar;
    }


    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/icons/profiles';
    }

    public function upload($profileForm,$fieldname=null)
    {
    if(!$fieldname) return; // Nofilefield used asa source
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
        $profileForm[$fieldname]->getData()->move(
       // $this->getAvatar()->move(
            $this->getUploadRootDir(),
            $profileForm[$fieldname]->getData()->getClientOriginalName()
        );

        // set the path property to the filename where you've saved the file
        $this->path = $profileForm[$fieldname]->getData()->getClientOriginalName();

        // clean up the file property as you won't need it anymore
       // $this->avatar = null;
    }
}