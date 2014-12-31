<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 7/26/13
 * Time: 10:31 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Gladtur\TagBundle\Entity;

use Doctrine\ORM\EntityRepository;
use DoctrineExtensions\Query\Mysql;

class LocationRepository extends EntityRepository
{
    private $tableModelRef = 'Gladtur\TagBundle\Entity\Location';
    private $userDataModelRef = 'Gladtur\TagBundle\Entity\UserLocationData';
    private $tagsTableModelRef = 'Gladtur\TagBundle\Entity\UserLocationTagData';


    public function getAllLocationsCount($locationTopcategoryId = null)
    {
        if (!$locationTopcategoryId) {
            return $this->_em->createQueryBuilder()->select("count(l)")->from($this->tableModelRef, 'l')->where(
                'l.published=1'
            )->getQuery()->getSingleScalarResult();
        } else {
            return $this->_em->createQueryBuilder()->select("count(l)")->from($this->tableModelRef, 'l')->where(
                'l.published=1'
            )->andWhere('l.location_top_category=' . $locationTopcategoryId)->getQuery()->getSingleScalarResult();
        }
    }

    public function getPlacesOrderByDistance(
        $user,
        $userProfile,
        $userLatitude,
        $userLongitude,
        $locationTopcategoryId,
        $page = 0,
        $perPage = 10,
        $orderDir = 'ASC'
    ) {
        // Make Doctrine use trig-math functions
        /*file_put_contents('/usr/share/nginx/getPlacesOrderByDistance_'.time(), json_encode(array('user'=>$user, 'profile'=>$userProfile, 'lat'=>$userLatitude, 'lon'=>$userLongitude, 'topcategory'=>$locationTopcategoryId, 'page'=>$page, 'perPage'=>$perPage)));*/
        $this->_em->getConfiguration()->addCustomNumericFunction('RADIANS', 'DoctrineExtensions\Query\Mysql\Radians');
        $this->_em->getConfiguration()->addCustomNumericFunction('COS', 'DoctrineExtensions\Query\Mysql\Cos');
        $this->_em->getConfiguration()->addCustomNumericFunction('ACOS', 'DoctrineExtensions\Query\Mysql\Acos');
        $this->_em->getConfiguration()->addCustomNumericFunction('SIN', 'DoctrineExtensions\Query\Mysql\Sin');
        $dql = $this->_em->createQueryBuilder()->select("l")->addSelect(
            "(6371000 * ACOS(SIN(RADIANS($userLatitude)) * SIN(RADIANS(l.latitude)) + COS(RADIANS($userLatitude)) * COS(RADIANS(l.latitude)) * COS(RADIANS(l.longitude) - RADIANS($userLongitude)))) as distance"
        )->from($this->tableModelRef, 'l')->where('l.published=1')->having('distance < 1000000')->setMaxResults(
            $perPage
        )->setFirstResult($page * $perPage);
        if ($locationTopcategoryId) {
            $dql->andWhere('l.location_top_category=' . $locationTopcategoryId);
        }
        $locationsArray = $dql->addOrderBy('distance')->getQuery()->getArrayResult();
        $locations = array();
        foreach ($locationsArray as $locSrcData) {
            $location = $this->_em->find($this->tableModelRef, $locSrcData[0]['id']);
            $tagsandValues = $this->_repolocationTagsandValues($location, $userProfile, $user);
            $score = -1;
            if (count($tagsandValues) > 0) {
                $tags_sum = 0;
                foreach ($tagsandValues as $tagId => $tagPropertiesAssoc) {
                    $tags_sum += intval($tagPropertiesAssoc['value']);
                }
                $score = $tags_sum / count($tagsandValues);
            }
            if (($score > 1) && ($score < 2)) {
                $score = 0;
            }
            if ($score == -1) {
                $score = 3;
            }
            $locAddressAssoc = $location->getAddressAssoc();
            $thumbnail = ($locSrcData[0]['mainImageThumbnail']) ? "http://gladtur.dk/uploads/avalanche/thumbnail/locations/" . $locSrcData[0]['id'] . "/" . $locSrcData[0]['mainImageThumbnail'] : "http://gladtur.dk/uploads/avalanche/thumbnail/noimage.png";
            $locations[] = array(
                'id' => $locSrcData[0]['id'],
                'topcatid' => intval($locationTopcategoryId),
                'name' => str_replace('&#39;', '\'', html_entity_decode($locSrcData[0]['readableName'])),
                'score' => $score,
                'distance' => intval($locSrcData['distance']),
                'thumbnail' => $thumbnail,
                'lat' => $locSrcData[0]['latitude'],
                'lon' => $locSrcData[0]['longitude'],
                'address' => $locAddressAssoc
            );
        }

        return $locations;
    }

    public function getTagValue(UserLocationTagData $tag_data)
    {
        return $this->_em->createQuery(
            "select tagdata.tagvalue from ' . $this->tagsTableModelRef . ' tagdata where tagdata.location=" . $tag_data->getLocation(
            )->getId() . " and tagdata.tag = " . $tag_data->getTag()->getId(
            ) . " and tagdata.tag_id=tagdata.user_profile = " . $tag_data->getUserProfile()->getId()
        )->getResult();
    }

    public function getTagsValues(Location $location)
    {
        //$locationTagsValuesQuery = $this->_em->createQueryBuilder()->select();
        $locationTagsValues = $this->_em->createQuery(
            "select identity(tagdata.tag) as tag, tagdata.tagvalue from " . $this->tagsTableModelRef . " tagdata where tagdata.location=" . $location->getId(
            ) . " order by tagdata.updated ASC"
        )->getArrayResult();
        $locTagsandValues = array();
        foreach ($locationTagsValues as $locationTagAndValue) {
            $locTagsandValues[] = array(
                'id' => $locationTagAndValue['tag'],
                'value' => $locationTagAndValue['tagvalue']
            );
        }

        return $locTagsandValues;
    }

    /**
     * Integer score-values have these semantic meanings:
     * neutral (0), bad(1) and perfect(2), unrated(3)
     */

    public function getScoreval($location, $userprofile)
    {
        $yesCount = 0;
        $noCount = 0;
        $neutralCount = 0;
        if (is_numeric($userprofile)) {
            $userprofileId = $userprofile;
            if ($userprofileId == 0) $userprofileId = 3;
        } else {
            $userprofileId = $userprofile->getId();
        }
        $scoreRSRows = $this->getEntityManager()->createQuery(
            "select identity(tagdata.tag) as tag, tagdata.tagvalue from " . $this->tagsTableModelRef . " tagdata where tagdata.location=" . $location->getId(
            ) . " and tagdata.user_profile = " . $userprofileId . " order by tagdata.updated ASC"
        )->getArrayResult();
        $uniqueTags = array();
        $scoreResultCount = count($scoreRSRows);
        if ($scoreResultCount == 0) {
            return 3;
        } else {
            foreach ($scoreRSRows as $tagIdAndValue) {
                $uniqueTags[$tagIdAndValue['tag']] = $tagIdAndValue['tagvalue'];
            }

            foreach ($uniqueTags as $uniqueTagId => $tagValue) {
                if ($tagValue == 1) {
                    $noCount++;
                }
                if ($tagValue == 2) {
                    $yesCount++;
                }
                if ($tagValue == 0) {
                    $neutralCount++;
                }
            }
            $uniqueScoreCount = count($uniqueTags);
            if ($neutralCount == $uniqueScoreCount) return 0;
            if (($yesCount == $uniqueScoreCount) || (($yesCount + $neutralCount) == $uniqueScoreCount)) return 2;
            if ($noCount == $uniqueScoreCount || (($noCount + $neutralCount) == $uniqueScoreCount)) return 1;
        }

        /*$aveScoreVal = $em->createQuery("select AVG(tagdata.tagvalue) from Gladtur\TagBundle\Entity\UserLocationTagData tagdata where tagdata.location=" . $location->getId()." and tagdata.user_profile=".$userprofile->getId()." ")->getSingleScalarResult();
      /*  $maxScoreVal = $this->_em->createQuery("select MAX(tagdata.tagvalue) from Gladtur\TagBundle\Entity\UserLocationTagData tagdata where tagdata.location=" . $location->getId()." and tagdata.user_profile=".$userprofile->getId())->getSingleScalarResult();
        $minScoreVal = $this->_em->createQuery("select MIN(tagdata.tagvalue) from Gladtur\TagBundle\Entity\UserLocationTagData tagdata where tagdata.location=" . $location->getId()." and tagdata.user_profile=".$userprofile->getId())->getSingleScalarResult();
        if(!$maxScoreVal) return 3; // Unrated! Not in the DB!
        if(($maxScoreVal == 1) && ($minScoreVal == 1)) return 1;
        if(($maxScoreVal == 2) && ($minScoreVal == 2)) return 2;*/
    }

    function getScoreName($location, $userprofile)
    {
        if ($this->getScoreval($location, $userprofile) == 0) return 'neutral';
        if ($this->getScoreval($location, $userprofile) == 1) return 'down';
        if ($this->getScoreval($location, $userprofile) == 2) return 'up';

        return 'unrated';
    }

    public function getResultsForCategoryAndPage($em, $categoryId, $perPage = 100, $page = 1)
    {
        //      return $em->createQuery('select l from ' . $tableModelRef . ' l, Gladtur\TagBundle\LocationCategory lc where lc.id=' .$categoryId. ' and l.location_top_category = ' . $categoryId . 'and l.published=1 order by l.readableName ASC')->setMaxResults($perPage)->getResult();
        return $em->createQuery(
            'select l from Gladtur\TagBundle\Entity\LocationCategory lc JOIN ' . $this->tableModelRef . ' l where lc.id=' . $categoryId . ' and l.published=1 order by l.readableName ASC'
        )->setMaxResults($perPage)->getResult();
    }

    public function getResultsForTopCategoryAndPage($em, $topCategoryId, $perPage = 100, $page = 1)
    {
        return $em->createQuery(
            'select l from ' . $this->tableModelRef . ' l where l.location_top_category = ' . $topCategoryId . 'and l.published=1 and l.slug is not null order by l.readableName ASC'
        )->setMaxResults($perPage)->getResult();
    }

    public function getResultsForPage($em, $perPage = 100, $page = 1)
    {
        return $em->createQuery(
            'select l from ' . $this->tableModelRef . ' l where l.published=1 and l.slug is not null order by l.readableName ASC'
        )->setMaxResults($perPage)->getResult();
    }

    public function getRecents($em, $limit = 5)
    {
        return $em->createQuery(
            'select l from ' . $this->tableModelRef . ' l where l.location_top_category is not null and l.published=1 and l.slug is not null order by l.id DESC'
        )->setMaxResults($limit)->getResult();
    }

    public function getCategories($em)
    {
        return $em->createQuery(
            'select l from ' . $this->tableModelRef . ' l where l=' . parent . ' and l.published=1 order by l.readableName ASC'
        )->setMaxResults($limit)->getResult();
    }

    public function getTagsandValues($location, $profile, $user = null)
    {
        $profileTagsRs = array();
        $em = $this->_em;
        $profileqb = $em->createQueryBuilder('profiletags');
        $tagvaluesqb = $em->createQueryBuilder('tagvalues');
        if (!$profile->getIndividualized()) {
            // Get all tags for a general non-individualized profile
            $profileTagsQb = $profileqb->select(
                array(
                    'profiletags.id id',
                    'profiletags.readableName',
                    'profiletags.textDescription',
                    'profiletags.iconPath icon'
                )
            )->from('Gladtur\TagBundle\Entity\TvguserProfile', 'uprofile')->join('uprofile.tags', 'profiletags')->where(
                'uprofile.id = ' . $profile->getId()
            );
            $profileTagsRs = $profileTagsQb->getQuery()->getArrayResult();
        } elseif ($user) {
            $profile = $user->getFreeProfile();
            if ($profile && $profile->getProfileActive()) {
                $userTagsRs = $profile->getProfileTags();
                foreach ($userTagsRs as $tag) {
                    $profileTagsRs[] = array(
                        'id' => $tag->getId(),
                        'readableName' => $tag->getReadableName(),
                        'textDescription' => $tag->getTextDescription(),
                        'icon' => $tag->getIconPathRaw()
                    );
                }
            }
        }
        $profileTagIds = array();
        $profileTagAssoc = array();
        foreach ($profileTagsRs as $profileTag) {
            $profileTagIds[] = $profileTag['id'];
            $profileTagAssoc[$profileTag['id']] = array(
                'name' => $profileTag['readableName'],
                'info' => $profileTag['textDescription'],
                'icon' => 'http://gladtur.dk/uploads/icons/tags/' . $profileTag['icon']
            );
        }
        $tagvaluesQb = $tagvaluesqb->select(array('ultd.tag'))->from(
            'Gladtur\TagBundle\Entity\UserLocationTagData',
            'ultd'
        )->where('ultd.location = ' . $location->getId())->andWhere('ultd.tag IN (:tagids)')->setParameter(
            'tagids',
            $profileTagIds
        )->orderBy('ultd.created', 'ASC');
        $tagEntities = $tagvaluesQb->getQuery()->getResult(); // getArrayResult for assoc arrays ! //
        /*   $tagRs = array();
           foreach($tagEntities as $tagIdAndValueTuple){
               $tagRs[$tagIdAndValueTuple['tagid']] = array('id'=>$tagIdAndValueTuple['tagid'], 'value'=>$tagIdAndValueTuple['tagvalue'], 'name' => $profileTagAssoc[$tagIdAndValueTuple['tagid']]['name'], 'info' => $profileTagAssoc[$tagIdAndValueTuple['tagid']]['info'], 'icon' => $profileTagAssoc[$tagIdAndValueTuple['tagid']]['icon']);
           }*/

        return $tagEntities;
    }

    public function getLikelySlugs($slugCandidate = '')
    {
        $slugsAssoc = $this->_em->createQuery(
            "select location.slug, location.slug_no from " . $this->tableModelRef . " location where location.published=1 and location.slug LIKE '%" . $slugCandidate . "%'"
        )->getArrayResult();
        $slugCandidates = array();
        foreach ($slugsAssoc as $slug) {
            $slugCandidates[] = $slug['slug'];
        }

        return $slugCandidates;
    }

    public function getMaximumSlugNoForSlug($baseSlug = '')
    {
        $slugsAssoc = $this->_em->createQuery(
            "select max(location.slug_no) from " . $this->tableModelRef . " location where location.published=1 and location.slug LIKE '%" . $baseSlug . "%'"
        )->getSingleScalarResult();
    }

    public function _repolocationTagsandValues($location, $profile, $user)
    {
        $profileTagsRs = array();
        // Get the profile from a local selection first, and if not set then from the active user.
        $profileqb = $this->_em->createQueryBuilder('profiletags');
        $tagvaluesqb = $this->_em->createQueryBuilder('tagvalues');
        if (!$profile->getIndividualized()) {
            // Get all tags for a general non-individualized profile
            /*$profileTagsQb = $profileqb->select(array('profiletags.id id', 'profiletags.readableName', 'profiletags.textDescription', 'profiletags.iconPath icon'))->from('Gladtur\TagBundle\Entity\TvguserProfile', 'uprofile')->join('uprofile.tags', 'profiletags')->join('profiletags.location_categories', 'tag_locationcategories')->where('uprofile.id = '.$profile->getId());
            $profileTagsRs = $profileTagsQb->getQuery()->getArrayResult();*/
            $userTagsRs = $profile->getTags($location->getTopCategory()->getId());
        } else {
            //$profile = $this->getUser()->getFreeProfile();
            $profile = $user->getFreeProfile();
            if ($profile && $profile->getProfileActive()) {
                $userTagsRs = $profile->getProfileTags($location->getTopCategory()->getId());
            }
        }
        foreach ($userTagsRs as $tag) {
            $profileTagsRs[] = array(
                'id' => $tag->getId(),
                'readableName' => $tag->getReadableName(),
                'textDescription' => $tag->getTextDescription(),
                'icon' => $tag->getIconPathRaw()
            );
        }
        $profileTagIds = array();
        $profileTagAssoc = array();
        foreach ($profileTagsRs as $profileTag) {
            $profileTagIds[] = $profileTag['id'];
            $profileTagAssoc[$profileTag['id']] = array(
                'name' => $profileTag['readableName'],
                'info' => $profileTag['textDescription'],
                'icon' => '/uploads/icons/tags/' . $profileTag['icon']
            );
        }
        $tagvaluesQb = $tagvaluesqb->select(array('identity(ultd.tag) tagid', 'ultd.tagvalue tagvalue'))->from(
            'Gladtur\TagBundle\Entity\UserLocationTagData',
            'ultd'
        )->where('ultd.location = ' . $location->getId())->andWhere('ultd.tag IN (:tagids)')->andWhere(
            'ultd.tagvalue IN (1,2)'
        )->setParameter('tagids', $profileTagIds)->orderBy('ultd.created', 'ASC');
        $tagvalues = $tagvaluesQb->getQuery()->getArrayResult();
        $tagRs = array();
        foreach ($tagvalues as $tagIdAndValueTuple) {
            $tagRs[$tagIdAndValueTuple['tagid']] = array(
                'id' => $tagIdAndValueTuple['tagid'],
                'value' => $tagIdAndValueTuple['tagvalue'],
                'name' => $profileTagAssoc[$tagIdAndValueTuple['tagid']]['name'],
                'info' => $profileTagAssoc[$tagIdAndValueTuple['tagid']]['info'],
                'icon' => $profileTagAssoc[$tagIdAndValueTuple['tagid']]['icon']
            );
        }

        return $tagRs;
    }
}