<?php
namespace Gladtur\TagBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\OneToOne(targetEntity="User", inversedBy="profile")
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
}