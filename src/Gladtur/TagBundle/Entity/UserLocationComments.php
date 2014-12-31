<?php
namespace Gladtur\TagBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Uploadable\Fixture\Entity\File;


/**
 * Gladtur\TagBundle\Entity\UserLocationComments
 *
 * @ORM\Table(name="user_location_comments")
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class UserLocationComments
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="location_comments", fetch="EXTRA_LAZY")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Location", inversedBy="user_comments", fetch="EXTRA_LAZY")
     */
    private $location;

    /**
     * @ORM\ManyToOne(targetEntity="TvguserProfile", inversedBy="user_location_comments", fetch="EXTRA_LAZY")
     */
    private $profile;


    /**
     * @ORM\OneToMany(targetEntity="CommentMedia", mappedBy="comment", fetch="EXTRA_LAZY")
     */
    private $media;

    // Virtual field not to be persisted in DB
    /**
     * @Assert\File(
     *     maxSize = "5M",
     *     mimeTypes = {"image/png", "image/jpeg", "image/jpg"},
     *     mimeTypesMessage = "Venligst upload et gyldigt billede - Af typen PNG eller JPG og maksimum 1 MegaByte",
     *     maxSizeMessage = "Billedets fil-størrelse er for stort ({{size}} {{suffix}}). Du må maks. uploade billeder på {{limit}} {{suffix}}"
     * )
     */
    private $comment_image;

    /**
     * @param mixed $comment_image
     */
    public function setCommentImage($comment_image)
    {
        $this->comment_image = $comment_image;
    }

    /**
     * @return mixed
     */
    public function getCommentImage()
    {
        return $this->comment_image;
    }


    public function addMedia(CommentMedia $media)
    {
        $this->media->add($media);
    }

    public function removeMedia(CommentMedia $media)
    {
        $this->media->removeElement($media);
    }

    /**
     * @ORM\Column(name="comment_txt", type="text", nullable=true)
     * @Assert\NotBlank(message = "Kommentaren må ikke være blank")
     * @Assert\Length(
     *      min = "5",
     *      max = "2048",
     *      minMessage = "Din kommentar skal mindst have {{ limit }} tegn/bogstaver",
     *      maxMessage = "Din kommentar er for lang... Maks. {{ limit }} tegn/bogstaver"
     * )
     */
    private $comment_txt;

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
     * @param mixed $comment_txt
     */

    public function __construct()
    {
        $this->media = new ArrayCollection();
    }

    public function setCommentTxt($comment_txt)
    {
        $this->comment_txt = $comment_txt;
    }

    /**
     * @return mixed
     */
    public function getCommentTxt()
    {
        return $this->comment_txt;
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
     * @param mixed $profile
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;
    }

    /**
     * @return mixed
     */
    public function getProfile()
    {
        return $this->profile;
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return int
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @return int
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @return ArrayCollection
     */
    public function getMedia()
    {
        return $this->media;
    }

    public function getMediaAssoc()
    {
        $mediaAssoc = array();
        /**
         * @var CommentMedia $media
         */
        foreach ($this->getMedia()->toArray() as $media) {
            $mediaAssoc[] = array('delta' => $media->getDelta(), 'URL' => $media->getMediaPath());
        }

        return $mediaAssoc;
    }

    public function getAddedmedia()
    {
        return new File();
    }

    public function setAddedmedia()
    {
        return '';
    }

}