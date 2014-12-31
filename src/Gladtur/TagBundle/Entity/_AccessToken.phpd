<?php
namespace Gladtur\TagBundle\Entity;

use FOS\OAuthServerBundle\Entity\AccessToken as BaseAccessToken;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class AccessToken extends BaseAccessToken
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
     * @var User $user;
     */
    protected $user;

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->getToken();
        //return 'OGYzYTIyZTRiZTA2ZTljNjkwYjEzMWMzNTJlNTI1MDVlNjVkMDRkOTMyMzU5ZjU4YTI4YjhiOTg5YmYxY2I5NA';//$this->getToken($this->user->getId());
    }
}
