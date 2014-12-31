<?php
/**
 * Created by PhpStorm.
 * User: mortenthorpe
 * Date: 10/07/14
 * Time: 13.40
 */

namespace Gladtur\TagBundle\Entity;

use Doctrine\ORM\EntityRepository;

class TvguserProfileRepository extends EntityRepository
{
    public function getIndividualizedProfileNamesArr()
    {
        return $this->_em->createQuery(
            'select profile.readableName from Gladtur\TagBundle\Entity\TvguserProfile profile where profile.individualized = true order by profile.rank ASC'
        )->getArrayResult();
    }

    public function getIndividualizedProfileIdsArr()
    {
        return $this->_em->createQuery(
            'select profile.id from Gladtur\TagBundle\Entity\TvguserProfile profile where profile.individualized = true order by profile.rank ASC'
        )->getArrayResult();
    }
} 