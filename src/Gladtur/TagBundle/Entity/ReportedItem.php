<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 7/24/13
 * Time: 10:02 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Gladtur\TagBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class ReportedItem
 * @package Gladtur\TagBundle\Entity
 * @ORM\Table(name="reported_item")
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class ReportedItem
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
     * @ORM\Column(name="kindID", type="integer", nullable=false)
     */
    private $kindID;

    /**
     * @ORM\Column(name="foreignKeyId", type="integer", nullable=false)
     */
    private $foreign_key_id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="report_items")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="TvguserProfile", inversedBy="report_items")
     */
    private $user_profile;
    /**
     * @ORM\Column(name="report_txt", type="text", nullable=false)
     */
    private $user_report_txt;

    /**
     * @ORM\Column(name="admin_response_txt", type="text", nullable=true)
     */
    private $admin_response_txt;

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
     * @param mixed $admin_response_txt
     */
    public function setAdminResponseTxt($admin_response_txt)
    {
        $this->admin_response_txt = $admin_response_txt;
    }

    /**
     * @return mixed
     */
    public function getAdminResponseTxt()
    {
        return $this->admin_response_txt;
    }

    /**
     * @param int $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return int
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param int $deletedAt
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * @return int
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param mixed $foreign_key_id
     */
    public function setForeignKeyId($foreign_key_id)
    {
        $this->foreign_key_id = $foreign_key_id;
    }

    /**
     * @return mixed
     */
    public function getForeignKeyId()
    {
        return $this->foreign_key_id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $kindID
     */
    public function setKindID($kindID)
    {
        $this->kindID = $kindID;
    }

    /**
     * @return mixed
     */
    public function getKindID()
    {
        return $this->kindID;
    }

    /**
     * @param int $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return int
     */
    public function getUpdated()
    {
        return $this->updated;
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
     * @param mixed $user_report_txt
     */
    public function setUserReportTxt($user_report_txt)
    {
        $this->user_report_txt = $user_report_txt;
    }

    /**
     * @return mixed
     */
    public function getUserReportTxt()
    {
        return $this->user_report_txt;
    }

    /**
     * @param mixed $user_profile
     */
    public function setUserProfile($user_profile)
    {
        $this->user_profile = $user_profile;
    }

    /**
     * @return mixed
     */
    public function getUserProfile()
    {
        return $this->user_profile;
    }

}