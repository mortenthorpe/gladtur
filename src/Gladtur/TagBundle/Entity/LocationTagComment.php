<?php

namespace Gladtur\TagBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Gladtur\TagBundle\Entity\LocationTagComment
 *
 * @ORM\Table(name="location_tag_comment")
 * @ORM\Entity
 */
class LocationTagComment
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
     * @var integer $locationTagId
     *
     * @ORM\Column(name="location_tag_id", type="integer", nullable=true)
     */
    private $locationTagId;

    /**
     * @var string $commenttext
     *
     * @ORM\Column(name="commenttext", type="text", nullable=true)
     */
    private $commenttext;

    /**
     * @var string $mediaFilepath
     *
     * @ORM\Column(name="media_filepath", type="string", length=255, nullable=true)
     */
    private $mediaFilepath;



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
     * Set locationTagId
     *
     * @param integer $locationTagId
     * @return LocationTagComment
     */
    public function setLocationTagId($locationTagId)
    {
        $this->locationTagId = $locationTagId;
    
        return $this;
    }

    /**
     * Get locationTagId
     *
     * @return integer 
     */
    public function getLocationTagId()
    {
        return $this->locationTagId;
    }

    /**
     * Set commenttext
     *
     * @param string $commenttext
     * @return LocationTagComment
     */
    public function setCommenttext($commenttext)
    {
        $this->commenttext = $commenttext;
    
        return $this;
    }

    /**
     * Get commenttext
     *
     * @return string 
     */
    public function getCommenttext()
    {
        return $this->commenttext;
    }

    /**
     * Set mediaFilepath
     *
     * @param string $mediaFilepath
     * @return LocationTagComment
     */
    public function setMediaFilepath($mediaFilepath)
    {
        $this->mediaFilepath = $mediaFilepath;
    
        return $this;
    }

    /**
     * Get mediaFilepath
     *
     * @return string 
     */
    public function getMediaFilepath()
    {
        return $this->mediaFilepath;
    }
}