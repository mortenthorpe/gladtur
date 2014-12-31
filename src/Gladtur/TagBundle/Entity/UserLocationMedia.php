<?php
namespace Gladtur\TagBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Gladtur\TagBundle\Entity\UserLocationMedia
 *
 * @ORM\Table(name="user_location_media")
 * @ORM\Entity
 */
class UserLocationMedia
{
    private $myULDirPath; //The Upload path, relative to the starting point of /uploads in relation to webroot //
    private $emptyString;

    public function __construct()
    {
        $this->myULDirPath = 'locations';
        $this->emptyString = '- Ikke angivet -';
    }

    public function __toString()
    {
        return '<img src="' . $this->getMediaPath() . '" class="' . ($this->getIsmainimage(
        )) ? 'main' : 'subimage' . '"/>';
    }

    /**
     * @param string $myULDirPath
     */
    public function setMyULDirPath($myULDirPath)
    {
        $this->myULDirPath = $myULDirPath;
    }

    /**
     * @return string
     */
    public function getMyULDirPath()
    {
        return $this->myULDirPath;
    }


    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(name="delta", type="integer", nullable=true)
     */
    private $delta;

    /**
     * @param mixed $delta
     */
    public function setDelta($delta)
    {
        $this->delta = $delta;
    }

    /**
     * @return mixed
     */
    public function getDelta()
    {
        return $this->delta;
    }

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="user_media")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Location", inversedBy="user_media")
     */
    private $location;

    /**
     * @var boolean $published
     *
     * @ORM\Column(name="published", type="boolean", nullable=true)
     */
    private $published;

    /**
     * @Assert\File(
     *     maxSize = "5M",
     *     mimeTypes = {"image/png", "image/jpeg", "image/jpg"},
     *     mimeTypesMessage = "Venligst upload et gyldigt billede - Af typen PNG eller JPG og maksimum 1 MegaByte"
     * )
     */
    private $filepath;

    /**
     * @ORM\Column(name="is_mainimage", type="boolean", nullable=true)
     */
    private $ismainimage;

    /**
     * Get filepath.
     *
     * @return UploadedFile
     */
    public function getFilepath()
    {
        return $this->filepath;
    }

    /**
     * Sets filepath.
     *
     * @param UploadedFile $filepath
     */
    public function setFilepath(UploadedFile $file = null)
    {
        $this->filepath = $file;
    }

    /**
     * @ORM\Column(name="path", type="text")
     */
    private $media_path;

    /**
     * @var $media_mime
     *
     * @ORM\Column(name="mime", type="string", length=255, nullable=true)
     */
    private $media_mime;

    public function setMediaPath($media_path)
    {
        $this->media_path = $media_path;
    }

    /**
     * @return mixed
     */
    public function getMediaPath()
    {
        return $this->media_path;
    }

    public function getMediaMime()
    {
        return $this->media_mime;
    }

    public function setMediaMime($mimetype)
    {
        $this->media_mime = $mimetype;

        return $this;
    }

    /**
     * @param mixed $ismainimage
     */
    public function setIsmainimage($ismainimage)
    {
        $this->ismainimage = $ismainimage;
    }

    /**
     * @return mixed
     */
    public function getIsmainimage()
    {
        return $this->ismainimage;
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

    /**
     * @param boolean $published
     */
    public function setPublished($published)
    {
        $this->published = $published;
    }

    /**
     * @return boolean
     */
    public function getPublished()
    {
        return $this->published;
    }


}