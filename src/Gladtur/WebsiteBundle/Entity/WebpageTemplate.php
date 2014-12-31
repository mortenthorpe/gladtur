<?php
namespace Gladtur\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Webpage
 *
 * @ORM\Table("webpage_template")
 * @ORM\Entity
 */
class WebpageTemplate
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
     * @ORM\OneToMany(targetEntity="Webpage", mappedBy="templatename")
     */
    private $webpages;

    /**
     * @var string
     *
     * @ORM\Column(name="template_name", type="string", length=255)
     */
    private $template_name;

    /**
     * @var string
     *
     * @ORM\Column(name="template_tpl", type="string", length=255)
     */
    private $template_tpl;

    /**
     * @var string
     *
     * @ORM\Column(name="positions_assoc", type="text")
     */
    private $positions_assoc;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $positions_assoc
     */
    public function setPositionsAssoc($positions_assoc)
    {
        $this->positions_assoc = $positions_assoc;
    }

    /**
     * @return array
     */
    public function getPositionsAssoc()
    {
        return json_decode($this->positions_assoc, true);
    }

    /**
     * @param string $template_name
     */
    public function setTemplateName($template_name)
    {
        $this->template_name = $template_name;
    }

    /**
     * @return string
     */
    public function getTemplateName()
    {
        return $this->template_name;
    }

    /**
     * @param string $template_tpl
     */
    public function setTemplateTpl($template_tpl)
    {
        $this->template_tpl = $template_tpl;
    }

    /**
     * @return string
     */
    public function getTemplateTpl()
    {
        return $this->template_tpl;
    }

    public function getCountAtPosition($block_position)
    {
        $block_positions_all_assoc = $this->getPositionsAssoc();

        return (isset($block_positions_all_assoc[$block_position])) ? $block_positions_all_assoc[$block_position] : 5;
    }
} 