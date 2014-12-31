<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 7/19/13
 * Time: 11:45 AM
 * To change this template use File | Settings | File Templates.
 */
namespace Gladtur\TagBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class UserLocationHours
 * @ORM\Table(name="user_location_hours")
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class UserLocationHours
{
    private $closedTxt;

    public function __construct()
    {
        $this->isclosed = false;
        $this->closedTxt = 'LUKKET';
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
     * @ORM\ManyToOne(targetEntity="UserLocationData", inversedBy="daysHoursOpenClosed")
     */
    private $user_location_data;

    /**
     * @ORM\Column(name="day_number", type="integer", nullable=true)
     * 0-6 value
     */
    private $dayNumber;

    /**
     * @ORM\Column(name="time_opens", type="integer", nullable=true)
     */
    private $timeOpens;

    /**
     * @ORM\Column(name="time_closes", type="integer", nullable=true)
     */
    private $timeCloses;

    /**
     * @ORM\Column(name="opening_times_txt", type="string", nullable=true)
     */
    private $timesTxt; // Currently the ONLY parameter being used! //

    /**
     * @var boolean $isclosed
     * Pseudo-column NOT persisted to database currently!
     * Just for FORMs use for display and inline altering of other form fields
     */
    private $isclosed;
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param mixed $timesTxt
     */
    public function setTimesTxt($timesTxt)
    {
        $this->timesTxt = ($timesTxt == 1) ? $this->closedTxt : $timesTxt;
    }

    /**
     * @return mixed
     */
    public function getTimesTxt()
    {
        return $this->timesTxt;
    }

    /**
     * @param mixed $dayNumber
     */
    public function setDayNumber($dayNumber)
    {
        $this->dayNumber = $dayNumber;
    }

    /**
     * @return mixed
     */
    public function getDayNumber()
    {
        return $this->dayNumber;
    }

    /**
     * @param mixed $user_location_data
     */
    public function setUserLocationData($user_location_data)
    {
        $this->user_location_data = $user_location_data;
    }

    /**
     * @return mixed
     */
    public function getUserLocationData()
    {
        return $this->user_location_data;
    }

    public function getDayName()
    {
        $dayNamesDa = array(
            0 => 'Mandag',
            1 => 'Tirsdag',
            2 => 'Onsdag',
            3 => 'Torsdag',
            4 => 'Fredag',
            5 => 'Lørdag',
            6 => 'Søndag'
        );

        return $dayNamesDa[$this->getDayNumber()];
    }

    public function __toString()
    {
        return $this->getDayName() . ' - ' . $this->getTimesTxt();
    }

    public function getLabel()
    {
        return $this->getDayName();
    }

    /**
     * @param boolean $isclosed
     */
    public function setIsclosed($isclosed)
    {
        $this->isclosed = $isclosed;
        $this->setTimesTxt($this->closedTxt);
    }

    /**
     * @return boolean
     */
    public function getIsclosed()
    {
        if (!$this->isclosed) {
            return false;
        }

        return ((is_bool($this->isclosed) && $this->isclosed) || ($this->timesTxt == $this->closedTxt)) ? true : false;
    }
}