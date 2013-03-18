<?php

namespace Gladtur\TagBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Doctrine\UserManager as UserManager;
//use Gladtur\Entity\User as GladUser;
/**
 * Gladtur\TagBundle\Entity\UserLocationData
 *
 * @ORM\Table(name="user_location_data")
 * @ORM\Entity
 */

class UserLocationData{

    public function __construct(){
        //$this->location_data_users = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @var integer $createdAt
     *
     * @ORM\Column(name="created_at", type="integer", nullable=true)
     */
    protected $created_at;
    /**
    * @ORM\ManyToOne(targetEntity="User", inversedBy="userLocationData")
    */
    protected $user;

    public function getUser(){
        return $this->user;
    }
    /**
     * @ORM\ManyToOne(targetEntity="Location", inversedBy="userLocationData")
     */
    protected $location;

    public function getLocation(){
        return $this->location;
    }

    /**
     * @var integer $hoursOpeningtime
     *
     * @ORM\Column(name="hours_openingtime", type="integer", nullable=true)
     */
    private $hoursOpeningtime;
	
    /**
     * @var integer $hoursClosingtime
     *
     * @ORM\Column(name="hours_closingtime", type="integer", nullable=true)
     */
    private $hoursClosingtime;
	
	/**
	* @var string $mediapath
	*
	*@ORM\Column(name="mediapath", type="string", length=255, nullable=true)
	*/
	private $mediapath;
    /**
	/**
	* @var string $txtDescription
	*
	*@ORM\Column(name="txt_description", type="text", nullable=true)
	*/
	private $txtDescription;
    /**
	* @var string $txtDomment
	*
	*@ORM\Column(name="txt_comment", type="text", nullable=true)
	*/
	private $txt_description;
	

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
     * Set locationId
     *
     * @param integer $locationId
     * @return UserLocationData
     */
    public function setLocationId($locationId)
    {
        $this->locationId = $locationId;
    
        return $this;
    }

    /**
     * Get locationId
     *
     * @return integer 
     */
    public function getLocationId()
    {
        return $this->location->getId();
    }

    /**
     * Set hoursOpeningtime
     *
     * @param integer $hoursOpeningtime
     * @return UserLocationData
     */
    public function setHoursOpeningtime($hoursOpeningtime)
    {
        $this->hoursOpeningtime = $hoursOpeningtime;
    
        return $this;
    }

    /**
     * Get hoursOpeningtime
     *
     * @return integer 
     */
    public function getHoursOpeningtime()
    {
        return $this->hoursOpeningtime;
    }

    /**
     * Set hoursClosingtime
     *
     * @param integer $hoursClosingtime
     * @return UserLocationData
     */
    public function setHoursClosingtime($hoursClosingtime)
    {
        $this->hoursClosingtime = $hoursClosingtime;
    
        return $this;
    }

    /**
     * Get hoursClosingtime
     *
     * @return integer 
     */
    public function getHoursClosingtime()
    {
        return $this->hoursClosingtime;
    }

    /**
     * Set mediapath
     *
     * @param string $mediapath
     * @return UserLocationData
     */
    public function setMediapath($mediapath)
    {
        $this->mediapath = $mediapath;
    
        return $this;
    }

    /**
     * Get mediapath
     *
     * @return string 
     */
    public function getMediapath()
    {
        return $this->mediapath;
    }

    /**
     * Set txtDescription
     *
     * @param string $txtDescription
     * @return UserLocationData
     */
    public function setTxtDescription($txtDescription)
    {
        $this->txtDescription = $txtDescription;
    
        return $this;
    }

    /**
     * Get txtDescription
     *
     * @return string 
     */
    public function getTxtDescription()
    {
        return $this->txtDescription;
    }
    
    public function __toString(){
        return (string) $this->getUser();
    }

    /**
     * Get LocationDataUsers
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getLocationDataUsers(){
        return $this->location_data_users;
    }

    public function getAuthor(){

    }
}
