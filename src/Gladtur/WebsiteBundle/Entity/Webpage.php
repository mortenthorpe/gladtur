<?php

namespace Gladtur\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Webpage
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Webpage
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity="WebpageTemplate", inversedBy="webpages")
     */

    private $templatename;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="WebpageSlot", mappedBy = "webpage")
     */
    private $slots;

    /**
     * @ORM\Column(name = "published", type="boolean", options = {"default": false})
     */
    private $published;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_description", type="text")
     */
    private $meta_description;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_keywords", type="text")
     */
    private $meta_keywords;

    /**
     * @var string
     *
     * @ORM\Column(name="pagetitle", type="text")
     */
    private $pagetitle;


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
     * Set slug
     *
     * @param string $slug
     *
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return WebpageTpl
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $published
     */
    public function setPublished($published)
    {
        $this->published = $published;
    }

    /**
     * @return mixed
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * @param string $meta_description
     */
    public function setMetaDescription($meta_description)
    {
        $this->meta_description = $meta_description;
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->meta_description;
    }

    /**
     * @param string $meta_keywords
     */
    public function setMetaKeywords($meta_keywords)
    {
        $this->meta_keywords = $meta_keywords;
    }

    /**
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->meta_keywords;
    }

    /**
     * @param string $pagetitle
     */
    public function setPagetitle($pagetitle)
    {
        $this->pagetitle = $pagetitle;
    }

    /**
     * @return string
     */
    public function getPagetitle()
    {
        return $this->pagetitle;
    }

    /**
     * @param mixed $slots
     */
    public function setSlots($slots)
    {
        $this->slots = $slots;
    }

    /**
     * @return mixed
     */
    public function getSlots()
    {
        return $this->slots;
    }

    /**
     * @param mixed $templatename
     */
    public function setTemplatename($templatename)
    {
        $this->templatename = $templatename;
    }

    /**
     * @return mixed
     */
    public function getTemplatename()
    {
        return $this->templatename;
    }

    public function getSlotsAtBlockPosition($position_name)
    {
        $allSlots = $this->getSlots();
        $slots = array();
        foreach ($allSlots as $slot) {
            if ($slot->getPublished() && ($slot->getBlockPosition() == $position_name)) {
                $slots[$slot->getRank()] = $slot;
            }
        }
        ksort($slots);
        return $slots;
    }

    public function computeETag(){
        return md5($this->getId().json_encode($this->getSlots()));
    }
}

