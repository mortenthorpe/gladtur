<?php

namespace Gladtur\MobileBundle\Controller;

use Gladtur\TagBundle\Controller\JsonController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends JsonController
{
    /**
     * @Route("globals")
     * @Method("GET")
     */
    public function globalsReplacementMapAction(){
        return parent::getJsonForData(array(
                'null_string'=>'',
                'null_integer' => 0,
                'null_float' => number_format(0,2),
                'null_image' => 'http://gladtur.dk/uploads/noimage.png',
            ));
    }

    /**
     * @Route("tagvaluesforlocation/{locationid}")
     * @Method("GET")
     */
    public function testTagValuesAction($locationid){
        $location = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\Location', $locationid);
        $tagValues = $this->getDoctrine()->getRepository('GladturTagBundle:Location')->getTagsValues($location);
        return parent::getJsonForData($tagValues);
    }

    /**
     * @Route("testscore/{locationid}/{profileid}")
     * @Method("GET")
     */
    public function testScoreAction($locationid, $profileid){
        $aveScoreRSRows = $this->getDoctrine()->getManager()->createQuery("select identity(tagdata.tag) as tag, tagdata.tagvalue from Gladtur\TagBundle\Entity\UserLocationTagData tagdata where tagdata.location=" . $locationid . " and tagdata.user_profile = " . $profileid . " order by tagdata.updated ASC")->getArrayResult();
        $uniqueTags = array();
        if(count($aveScoreRSRows)) {
            foreach($aveScoreRSRows as $tagIdAndValue){
                $uniqueTags[$tagIdAndValue['tag']] = $tagIdAndValue['tagvalue'];
            }
            $aveScoreVal = intval(array_sum($uniqueTags) / count($uniqueTags)); // Rounds down, as it casts float to integer
        }
        else {
            $aveScoreVal = 0;
        }
        return new JsonResponse($aveScoreVal);
    }

    /**
     * @Route("testtags/{locationid}/{profileid}/{userid}")
     * @Method("GET")
     */
    public function testTagsAction($locationid, $profileid, $userid = null){
        // Tags need to be returned thus:
        /**
         *  $propertiesAssoc[] = array('id'=>$tag->getId(), 'name'=>$tag->getReadableName(), 'info' => $tag->getTextDescription(), 'value' => $tagData->getTagvalue(), 'icon' => 'http://gladtur.dk/'.$tag->getPath());
         */
        $profileTagsRs = array();
        $profile = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\TvguserProfile', $profileid);
        $profileqb = $this->getDoctrine()->getManager()->createQueryBuilder('profiletags');
        $tagvaluesqb = $this->getDoctrine()->getManager()->createQueryBuilder('tagvalues');
        if(!$profile->getIndividualized()){
          // Get all tags for a general non-individualized profile
          $profileTagsQb = $profileqb->select(array('profiletags.id id', 'profiletags.readableName', 'profiletags.textDescription', 'profiletags.iconPath icon'))->from('Gladtur\TagBundle\Entity\TvguserProfile', 'uprofile')->join('uprofile.tags', 'profiletags')->where('uprofile.id = '.$profileid);
          $profileTagsRs = $profileTagsQb->getQuery()->getArrayResult();
        }
        elseif($userid){
            $um = $this->container->get('fos_user.user_manager');
            $user = $um->findUserBy(array('id'=>$userid));
            $profile = $user->getFreeProfile();
            if($profile && $profile->getProfileActive()){
                $userTagsRs = $profile->getProfileTags();
                foreach($userTagsRs as $tag){
                    $profileTagsRs[] = array('id' => $tag->getId(), 'readableName' => $tag->getReadableName(), 'textDescription' => $tag->getTextDescription(), 'icon' => $tag->getIconPathRaw());
                }
                return new JsonResponse($profileTagsRs);
            }
        }
        $profileTagIds = array();
        $profileTagAssoc = array();
        foreach($profileTagsRs as $profileTag){
            $profileTagIds[] = $profileTag['id'];
            $profileTagAssoc[$profileTag['id']] = array('name'=>$profileTag['readableName'], 'info' => $profileTag['textDescription'], 'icon'=>'http://gladtur.dk/uploads/icons/tags/'.$profileTag['icon']);
        }
        $tagvaluesQb = $tagvaluesqb->select(array('identity(ultd.tag) tagid', 'ultd.tagvalue tagvalue'))->from('Gladtur\TagBundle\Entity\UserLocationTagData', 'ultd')->where('ultd.location = '.$locationid)->andWhere('ultd.tag IN (:tagids)')->setParameter('tagids', $profileTagIds)->orderBy('ultd.created', 'ASC');
        $tagvalues = $tagvaluesQb->getQuery()->getArrayResult();
        $tagRs = array();
        foreach($tagvalues as $tagIdAndValueTuple){
            $tagRs[] = array('id'=>$tagIdAndValueTuple['tagid'], 'value'=>$tagIdAndValueTuple['tagvalue'], 'name' => $profileTagAssoc[$tagIdAndValueTuple['tagid']]['name'], 'info' => $profileTagAssoc[$tagIdAndValueTuple['tagid']]['info'], 'icon' => $profileTagAssoc[$tagIdAndValueTuple['tagid']]['icon']);
        }
        return new JsonResponse($tagRs);
    }
}