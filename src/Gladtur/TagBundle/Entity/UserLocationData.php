<?php

namespace Gladtur\TagBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Validator\Constraints as Assert;

//use Doctrine\ORM\EntityManager;

//use FOS\UserBundle\Doctrine\UserManager as UserManager;

/**
 * Gladtur\TagBundle\Entity\UserLocationData
 *
 * @ORM\Table(name="user_location_data")
 * @ORM\Entity
 */
class UserLocationData extends EntityRepository
{
    private $emptyString;

    public function __construct()
    {
        //$this->location_data_users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->created_at = time();
        $this->emptyString = '- Ikke angivet -';
        $this->daysHoursOpenClosed = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        return implode(' - ', array($this->getPhone(), $this->getMail(), $this->getContactPerson()));
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userLocationData", fetch="EXTRA_LAZY")
     */
    protected $user;

    /*
     * @return Gladtur\TagBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    public function setUser(\Gladtur\TagBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Location", inversedBy="userLocationData")
     */
    protected $location;

    /**
     * @ORM\ManyToOne(targetEntity="TvguserProfile", inversedBy="user_location_data")
     */
    private $profile;

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


    public function getLocation()
    {
        return $this->location;
    }

    public function setLocation(Location $location)
    {
        $this->location = $location;
    }

    public function getPublished()
    {
        return $this->location->getPublished();
    }

    /**
     * @ORM\Column(name="phone", type="string", nullable=true)
     */
    private $phone;

    /**
     * @var string $mail
     *
     * @ORM\Column(name="mail", type="string", length=255, nullable=true)
     * @Assert\Email(message = "Email skal være gyldig")
     */
    private $mail;

    /**
     * @var string $contactPerson
     *
     * @ORM\Column(name="contact_person", type="string", length=255, nullable=true)
     */
    private $contactPerson;

    /**
     * @var string $txtDescription
     *
     * @ORM\Column(name="txt_description", type="text", nullable=true)
     */
    private $txtDescription;

    /**
     * @var string $txtComment
     *
     * @ORM\Column(name="txt_comment", type="text", nullable=true)
     */
    private $txtComment;

    /**
     * @ORM\OneToMany(targetEntity="UserLocationHours", mappedBy="user_location_data", cascade={"persist"})
     * @ORM\OrderBy({"dayNumber" = "ASC"})
     */
    private $daysHoursOpenClosed;

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
        $this->txtComment = $txtComment;

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

    /**
     * Get LocationDataUsers
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getLocationDataUsers()
    {
        return $this->location_data_users;
    }

    public function getAuthor()
    {

    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone = '')
    {
        $this->phone = $phone;

        return $this;
    }

    public function getMail()
    {
        return $this->mail;
    }

    public function getMailValid()
    {
        return ($this->mail) ? true : false;
    }

    public function setMail($email = null)
    {
        $this->mail = $email;

        return $this;
    }

    public function getContactPerson()
    {
        return $this->contactPerson;
    }

    public function setContactPerson($personName = '')
    {
        $this->contactPerson = $personName;

        return $this;
    }

    public function addDaysHoursOpenClosed(UserLocationHours $dayHours)
    {
        $this->daysHoursOpenClosed->add($dayHours);
    }

    public function removeDaysHoursOpenClosed(UserLocationHours $dayHours)
    {
        $this->daysHoursOpenClosed->removeElement($dayHours);
    }

    /**
     * @return mixed
     */
    public function getDaysHoursOpenClosed()
    {
        if ($this->daysHoursOpenClosed->count() == 7) {
            return $this->daysHoursOpenClosed;
        }
        $rs = range(0, 6);
        foreach ($rs as $dayNumber) {
            $tmpDayHour = new UserLocationHours();
            $tmpDayHour->setDayNumber($dayNumber);
            $rs[$dayNumber] = $tmpDayHour;
        }
        foreach ($this->daysHoursOpenClosed as $dayHour) {
            $rs[$dayHour->getDayNumber()] = $dayHour;
        };

        return $rs;
    }

    public function getDaysHoursOpenClosedPadded()
    {
        //return $this->daysHoursOpenClosed;
        if ($this->daysHoursOpenClosed->count() == 7) {
            return $this->daysHoursOpenClosed;
        }
        $rs = range(0, 6);
        foreach ($rs as $dayNumber) {
            $tmpDayHour = new UserLocationHours();
            $tmpDayHour->setDayNumber($dayNumber);
            $rs[$dayNumber] = $tmpDayHour;
        }
        foreach ($this->daysHoursOpenClosed as $dayHour) {
            $rs[$dayHour->getDayNumber()] = $dayHour;
        };

        return $rs;
    }

    public function isEmptyRecord()
    {
        return !$this->getPhone() && !$this->getMail() && !$this->getContactPerson() && !$this->getTxtDescription(
        ) && ($this->getPhone() == '') && ($this->getMail() == '') && ($this->getContactPerson(
            ) == '') && ($this->getTxtDescription() == '');
    }

    private $locationData;
}
