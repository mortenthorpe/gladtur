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

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="UserLocationData", inversedBy="media")
     */
    protected $userLocationData;

    /**
     * @var boolean $published
     *
     * @ORM\Column(name="published", type="boolean", nullable=true)
     */
    private $published;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $filepath;


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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @var string $mimetype
     *
     * @ORM\Column(name="mimetype", type="string", length=255, nullable=true)
     */
    private $mimetype;

    public function getMimetype()
    {
        return $this->mimetype;
    }

    public function setMimetype(string $mimetype)
    {
        $this->mimetype = $mimetype;

        return $this;
    }
}