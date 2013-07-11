<?php
namespace Gladtur\TagBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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

    /**
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="profiles")
     */
    private $tags;

    /*public function getTags(){
        if(count($this->tags) == 0){
            return null;
        }
        else{
            return $this->tags;
        }
    }*/
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
     * @Assert\File(maxSize="6000000")
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;

    public function addTags(\Gladtur\TagBundle\Entity\Tag $tags){
        $this->tags[] = $tags;
        return $this;
    }

    public function removeTags(\Gladtur\TagBundle\Entity\Tag $tag){
        $this->tags->removeElement($tag);
    }

    public function getTags(){
        return $this->tags;
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


    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir() . '/' . $this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir() . '/' . $this->path;
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
	if($profileForm[$fieldname]->getData()){
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

    public function asJSON(){
        return array($this->getId(), $this->getReadableName());
    }

    public function __toString(){
        return $this->getReadableName();
    }
}
