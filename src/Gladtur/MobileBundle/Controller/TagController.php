<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 7/10/13
 * Time: 10:21 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Gladtur\MobileBundle\Controller;


use Gladtur\TagBundle\Entity\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use \Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\Request;
use Gladtur\TagBundle\Controller\JsonController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


class TagController extends JsonController {
    /**
     * @Route("tags")
     * @Method("GET")
     */
    public function getTagsAction(Request $request){
        $profileid = $request->get('profileid', null);
        $token = $request->get('token', null);
        $locationid = $request->get('locationid',null);
        $em = $this->getDoctrine()->getManager();
        $tags = array();
        $location = null;
        if($locationid){
            $location = $em->getRepository('Gladtur\TagBundle\Entity\Location')->find($locationid);
        }
        $tagEntities = array();
        if($profileid){
          $profile = $em->find('Gladtur\TagBundle\Entity\TvguserProfile', $profileid);
          if($profile && !$profile->getIndividualized()){
              if($location){
                $tagEntities = $profile->getTags($location->getTopCategory()->getId());
              }
              else{
                $tagEntities = $profile->getTags();
              }
          }
          // Dealing with a profile where relations to properties/tags are loosely defined.
          if($token && $profile && $profile->getIndividualized()){
              $um = $this->container->get('fos_user.user_manager');
              $user = $um->findUserBy(array('salt'=>$token));
              if($user){
                  $userProfileByTags = $user->getFreeProfile();
                  // The free profile is the active one for the user at the time of this request
                  if($userProfileByTags){
                  if($userProfileByTags->getProfileActive()){
                      if($location){
                        $tagEntities = $userProfileByTags->getProfileTags($location->getTopCategory()->getId());
                      }
                      else{
                        $tagEntities = $userProfileByTags->getProfileTags();
                      }
                  }
                  }
              }
          }
        }
        else{
          $tagEntities = $em->getRepository('GladturTagBundle:Tag')->findAll();
        }
             $tags = array();
             foreach($tagEntities as $tag){
                 if($tag->getPublished()){
                     $tags[] = array('id'=>$tag->getId(), 'name' => $tag->getReadableName(), 'info' => $tag->getTextDescription(), 'icon' => 'http://gladtur.dk/'.$tag->getPath());
                 }
             }
        return parent::getJsonForData($tags);
    }

}