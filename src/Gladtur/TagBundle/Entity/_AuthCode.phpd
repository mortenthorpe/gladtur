<?php

namespace Gladtur\TagBundle\Entity;

use FOS\OAuthServerBundle\Entity\AuthCode as BaseAuthCode;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class AuthCode extends BaseAuthCode
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumn(nullable=false)
     * @var Client $client
     */
    protected $client;

    /**
     * @ORM\ManyToOne(targetEntity="Gladtur\TagBundle\Entity\User")
     * @var User $user
     */
    protected $user;
}
