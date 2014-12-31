<?php
/**
 * Created by PhpStorm.
 * User: mortenthorpe
 * Date: 15/08/14
 * Time: 10.36
 */

namespace Gladtur\TagBundle\Entity;

use Doctrine\ORM\EntityRepository;
use DoctrineExtensions\Query\Mysql;


class _UserLocationTagDataRepository extends EntityRepository
{
    private $locationTableModelRef = 'Gladtur\TagBundle\Entity\Location';
    private $userDataModelRef = 'Gladtur\TagBundle\Entity\UserLocationData';
    private $tableModelRef = 'Gladtur\TagBundle\Entity\UserLocationTagData';


    public function getLocationTagsForCategory($location_category, $location_profile, $require_tag_profile = false)
    {
        $this->_em->getRepository('Gladtur\TagBundle\Entity\UserLocationTagData')->findBy(
            array('tag' => $tagObj, 'location' => $location /*, 'user_profile' => $profile*/, 'deletedAt' => null),
            array('id' => 'DESC'),
            1
        );
    }
}