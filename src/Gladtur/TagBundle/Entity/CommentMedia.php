<?php
namespace Gladtur\TagBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Gladtur\TagBundle\Entity\CommentMedia
 *
 * @ORM\Table(name="comment_media")
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class CommentMedia
{

    private $myULDirPath; //The Upload path, relative to the starting point of /uploads in relation to webroot //

    public function __construct()
    {
        $this->myULDirPath = 'comments';
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
     * @ORM\ManyToOne(targetEntity="UserLocationComments", inversedBy="media", fetch="EXTRA_LAZY")
     */
    private $comment;

    /**
     * @ORM\Column(name="path", type="text")
     * @Assert\File(
     *     maxSize = "5M",
     *     mimeTypes = {"image/png", "image/jpeg", "image/jpg"},
     *     mimeTypesMessage = "Venligst upload et gyldigt billede - Af typen PNG eller JPG og maksimum 1 MegaByte",
     *     maxSizeMessage = "Billedets fil-størrelse er for stort ({{size}} {{suffix}}). Du må maks. uploade billeder på {{limit}} {{suffix}}"
     * )
     */
    private $media_path;

    /**
     * @ORM\Column(name="mime", type="string", nullable=true)
     */
    private $media_mime;

    /**
     * @ORM\Column(name="delta", type="integer")
     */
    private $delta;

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
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
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
     * @param mixed $media_mime
     */
    public function setMediaMime($media_mime)
    {
        $this->media_mime = $media_mime;
    }

    /**
     * @return mixed
     */
    public function getMediaMime()
    {
        return $this->media_mime;
    }

    /**
     * @param mixed $media_path
     * $dirPath is the absolute dir-path, e.g.: $dirPath = $this->get('kernel')->getRootDir() . '/../web/uploads/comments/';
     */
    public function setMediaPath($media_path = '')
    {
        $this->media_path = $media_path;

        return;
    }

    /**
     * @return mixed
     */
    public function getMediaPath($thumbnail = true)
    {
        /*if($thumbnail){
            return dirname($this->media_path).'/thumbnails/'.basename($this->media_path);
        }*/
        return $this->media_path;
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    public function __toString()
    {
        return $this->media_path;
    }

}