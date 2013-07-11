<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 6/27/13
 * Time: 10:46 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Gladtur\TagBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_resetpassword")
 */
class UserPassword
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     *
     * @ORM\OneToOne(targetEntity="User", inversedBy="newpassword")
     */
    protected $user;

    /**
     * @var string $usernameoremail
     *
     * @ORM\Column(name="usernameoremail", type="string", length=255, nullable=false)
     */
    protected $usernameoremail;

    /**
     * @var string $newpassword_plain
     *
     * @ORM\Column(name="newpassword_plain", type="string", length=255, nullable=false)
     */
    protected $newpassword_plain;

    /**
     * @param string $newpassword_plain
     */
    public function setNewpasswordPlain($newpassword_plain)
    {
        $this->newpassword_plain = $newpassword_plain;
    }

    /**
     * @return string
     */
    public function getNewpasswordPlain()
    {
        return $this->newpassword_plain;
    }

    /**
     * @param string $usernameoremail
     */
    public function setUsernameoremail($usernameoremail)
    {
        $this->usernameoremail = $usernameoremail;
    }

    /**
     * @return string
     */
    public function getUsernameoremail()
    {
        return $this->usernameoremail;
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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}