<?php
// src/Acme/ApiBundle/Entity/Client.php

namespace Gladtur\TagBundle\Entity;

use FOS\OAuthServerBundle\Entity\Client as BaseClient;
use Doctrine\ORM\Mapping as ORM;
use FOS\OAuthServerBundle\Model\ClientManager;

/**
 * @ORM\Entity
 */
class Client extends BaseClient
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /*
     * @return string

    public function getPublicId()
    {
        return 'romain';
    }
     */
    /*
     * @return string
     */
    public function setRandomId($randomId = '')
    {
        $this->randomId = 'romain';
    }

    /*
     * @return null
     */
    public function setSecret($secret = '')
    {
        $this->secret = 'romain';
    }

    /*
     * @return Client

    public function findClientByPublicId($publicId)
    {
        $clients = $this->findClientBy(
            array(
                'randomId' => $randomId,
            )
        );

        return array_shift($clients);
    }
    */
}