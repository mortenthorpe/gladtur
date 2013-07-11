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


class TagController extends JsonController {
    /**
     * @Route("tags")
     */
    public function getTagsAction(Request $request){
        if($request->get('uprofile', null)){
            return parent::getJsonForData(array());
        }
        else{
             $em = $this->getDoctrine()->getManager();
             $tagEntities = $em->getRepository('GladturTagBundle:Tag')->findAll();
             $tags = array();
             foreach($tagEntities as $tag){
                 if($tag->getPublished()){
                     $tags[] = array('id'=>$tag->getId(), 'name' => $tag->getReadableName(), 'info' => $tag->getTextDescription());
                 }
             }
        }
        return parent::getJsonForData($tags);
    }
}