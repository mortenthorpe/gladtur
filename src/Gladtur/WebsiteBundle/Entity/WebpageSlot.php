<?php

namespace Gladtur\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WebpageSlot
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class WebpageSlot
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text")
     */
    private $title;

    /**
     * @ORM\Column(name="block_position", type="string", length=255)
     */
    private $block_position;

    /**
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(name = "published", type="boolean", options = {"default": true})
     */
    private $published;

    /**
     * @var string
     *
     * @ORM\Column(name="html", type="text")
     */
    private $html;

    /**
     * @ORM\Column(name="is_global", type="boolean" , options = {"default": false})
     */
    private $is_global;
    /**
     * @var string
     *
     * @ORM\Column(name="javascript_include_paths", type="text")
     */
    private $javascript_include_paths;

    /**
     * @var string
     *
     * @ORM\Column(name="stylesheet_include_paths", type="text")
     */
    private $stylesheet_include_paths;

    /**
     * @ORM\ManyToMany(targetEntity="Webpage", inversedBy = "slots")
     */
    private $webpage;

    /**
     * @ORM\Column(name="rank", type="integer")
     */
    private $rank;

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
     * Set name
     *
     * @param string $name
     *
     * @return WebpageSlot
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return WebpageSlot
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
     * @param string $html
     */
    public function setHtml($html)
    {
        $this->html = $html;
    }

    /**
     * @return string
     */
    public function getHtml()
    {
        return $this->html;
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
     * @param mixed $webpage
     */
    public function setWebpage($webpage)
    {
        $this->webpage = $webpage;
    }

    /**
     * @return mixed
     */
    public function getWebpage()
    {
        return $this->webpage;
    }

    /**
     * @param mixed $block_position
     */
    public function setBlockPosition($block_position)
    {
        $this->block_position = $block_position;
    }

    /**
     * @return mixed
     */
    public function getBlockPosition()
    {
        return $this->block_position;
    }

    /**
     * @param string $javascript_include_paths
     */
    public function setJavascriptIncludePaths($javascript_include_paths)
    {
        $this->javascript_include_paths = $javascript_include_paths;
    }

    /**
     * @return string
     */
    public function getJavascriptIncludePaths()
    {
        return $this->javascript_include_paths;
    }

    /**
     * @param string $stylesheet_include_paths
     */
    public function setStylesheetIncludePaths($stylesheet_include_paths)
    {
        $this->stylesheet_include_paths = $stylesheet_include_paths;
    }

    /**
     * @return string
     */
    public function getStylesheetIncludePaths()
    {
        return $this->stylesheet_include_paths;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $rank
     */
    public function setRank($rank)
    {
        $this->rank = $rank;
    }

    /**
     * @return mixed
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * @param mixed $is_global
     */
    public function setIsGlobal($is_global)
    {
        $this->is_global = $is_global;
    }

    /**
     * @return mixed
     */
    public function getIsGlobal()
    {
        return $this->is_global;
    }

}

