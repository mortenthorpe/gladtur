<?php

namespace Gladtur\TagBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Doctrine\UserManager as UserManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
/**
 * Gladtur\TagBundle\Entity\UserLocationData
 *
 * @ORM\Table(name="user_location_data")
 * @ORM\Entity
 */

class UserLocationData extends EntityRepository{
    protected $container;

    public function __construct(){
        //$this->_em = new EntityManager();

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

    /*
     * @return Gladtur\TagBundle\Entity\User
     */
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

    public function getPublished(){
        return $this->location->getPublished();
    }
    /**
     * @var string $readableName
     *
     * @ORM\Column(name="readable_name", type="string", length=255, nullable=true)
     */
    private $readableName;

    /**
     * @var string $latitude
     *
     * @ORM\Column(name="latitude", type="string", length=64, nullable=true)
     */
    private $latitude;

    public function getLatitude(){
        return $this->latitude;
    }

    public function setLatitude($latitude=''){
        $this->latitude = $latitude;
        return $this;
    }
    /**
     * @var string $longitude
     *
     * @ORM\Column(name="longitude", type="string", length=64, nullable=true)
     */
    private $longitude;

    public function getLongitude(){
        return $this->longitude;
    }

    public function setLongitude($longitude=''){
        $this->longitude = $longitude;
        return $this;
    }


    /**
     * @var string $addressZip
     *
     * @ORM\Column(name="address_zip", type="string", length=20, nullable=true)
     */
    private $addressZip;

    /**
     * @var string $addressCountry
     *
     * @ORM\Column(name="address_country", type="string", length=255, nullable=true)
     */
    private $addressCountry;

    /**
     * @var string $addressCity
     *
     * @ORM\Column(name="address_city", type="string", length=255, nullable=true)
     */
    private $addressCity;

    /**
     * @var string $addressStreet
     *
     * @ORM\Column(name="address_street", type="string", length=255, nullable=true)
     */
    private $addressStreet;

    /**
     * @var string $addressExtd
     *
     * @ORM\Column(name="address_extd", type="string", length=255, nullable=true)
     */
    private $addressExtd;

    /**
     * @var string $phone
     *
     * @ORM\Column(name="phone", type="string", length=20, nullable=true)
     */
    private $phone;

    /**
     * @var string $mail
     *
     * @ORM\Column(name="mail", type="string", length=255, nullable=true)
     */
    private $mail;

    /**
     * @var string $contactPerson
     *
     * @ORM\Column(name="contact_person", type="string", length=255, nullable=true)
     */
    private $contactPerson;

    /**
     * @var integer $hoursOpeningtime
     *
     * @ORM\Column(name="hours_openingtime", type="time", nullable=true)
     */
    private $hoursOpeningtime;
	
    /**
     * @var integer $hoursClosingtime
     *
     * @ORM\Column(name="hours_closingtime", type="time", nullable=true)
     */
    private $hoursClosingtime;

	/**
	* @var string $mediapath
	*
	*@ORM\Column(name="mediapath", type="string", length=255, nullable=true)
	*/
	private $mediapath;

	/**
	* @var string $txtDescription
	*
	*@ORM\Column(name="txt_description", type="text", nullable=true)
	*/
	private $txtDescription;

    /**
	* @var string $txtComment
	*
	*@ORM\Column(name="txt_comment", type="text", nullable=true)
	*/
	private $txtComment;
	

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

    /**
     * Set txtComment
     *
     * @param string $txtComment
     * @return UserLocationData
     */
    public function setTxtComment($txtComment)
    {
        $this->txtComment= $txtComment;

        return $this;
    }

    /**
     * Get txtComment
     *
     * @return string
     */
    public function getTxtComment()
    {
        return $this->txtComment;
    }
    
    /*public function __toString(){
        return (string) $this->getUser();
    }*/

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

    public function getAddressCity(){
        return $this->addressCity;
    }

    public function setAddressCity($city=''){
        $this->addressCity = $city;
        return $this;
    }

    public function getAddressStreet(){
        return $this->addressStreet;
    }

    public function setAddressStreet($street=''){
        $this->addressStreet = $street;
        return $this;
    }

    public function getAddressZip(){
        return $this->addressZip;
    }

    public function setAddressZip($zip = ''){
        $this->addressZip = $zip;
        return $this;
    }

    public function getReadableName(){
        return $this->readableName;
    }

    public function setReadableName($readableName=''){
        $this->readableName = $readableName;
        return $this;
    }

    public function getAddressCountry(){
        return $this->addressCountry;
    }

    public function setAddressCountry($countryCode='DK'){
        $this->addressCountry = $countryCode;
        return $this;
    }

    public function getAddressExtd(){
        return $this->addressExtd;
    }

    public function setAddressExtd($addExtd=''){
        $this->addressExtd = $addExtd;
        return $this;
    }

    public function getPhone(){
        return $this->phone;
    }

    public function setPhone($phone=''){
        $this->phone = $phone;
        return $this;
    }

    public function getMail(){
        return $this->mail;
    }

    public function setMail($email=null){
        $this->mail = $email;
        return $this;
    }

    public function getHomepage(){
        return $this->getLocation()->getHomepage();
    }

    public function setHomepage($homepage = 'http://www.tvglad.dk'){
        $this->getLocation()->setHomepage($homepage );
        return $this;
    }

    public function getContactPerson(){
        return $this->contactPerson;
    }

    public function setContactPerson($personName = ''){
        $this->contactPerson = $personName;
        return $this;
    }

    public $locationData;

    public function getLocationDataForMyProfilePeers(){
        //return $this->get('security.context');
        //$cur_usr= $this->get('security.context')->getToken()->getUser();
        /*return $this->container;
        $em = $this->getDoctrine()->getEntityManager();
        $dQuery=$em->createQuery("select d from Gladtur\TagBundle\Entity\UserLocationData d, FOS\UserBundle\Entity\User u where u.profile_id=1 and d.user_id = u.id");
        return $dQuery->getResult();*/
    }

    public function __toString(){
        return ($this->getReadableName()) ? $this->getReadableName() : '----';
    }
}