<?php
// MONGO & Doctrine 2: http://symfony.com/doc/2.2/bundles/DoctrineMongoDBBundle/index.html
/**
 * Created by PhpStorm.
 * User: mortenthorpe
 * Date: 26/01/14
 * Time: 15.41
 */

namespace Gladtur\TagBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 *
 * @ORM\Table(name="eventslogged")
 * @ORM\Entity
 */
class EventLogger
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    // System, Controller
    private $e_type;
    // Anonymous (NULL) or User-ID
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="events")
     */
    private $user;
    // Controller name, when $e_type is Controller
    private $e_controller;
    // Action name, when $e_type is Controller
    /**
     * @ORM\Column(name = "e_action", type="string", nullable=true)
     */
    private $e_action;

    // Error, Warning, Notice, Success
    private $err_level;
    private $is_error;
    private $is_request;
    private $is_response;
    private $req_reference;
    private $e_created;
    private $e_updated;
    // Holds the Unique stashed key to this DB-row when stashed into a static repository, thus nullifying all gauarantees of unique keys
    private $e_stashkey;
    private $e_rawdata;
    private $e_note;

    /**
     * @ORM\Column(type = "text", name="request_string")
     */
    private $requestString;


    private function setStashKey()
    {
        $this->e_stashkey = '';

        return $this;
    }

    public function getStashKey()
    {
        return $this->e_stashkey;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
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
     * @param mixed $requestString
     */
    public function setRequestString($requestString)
    {
        $this->requestString = $requestString;
    }

    /**
     * @return mixed
     */
    public function getRequestString()
    {
        return $this->requestString;
    }


} 