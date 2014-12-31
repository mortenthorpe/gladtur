<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 6/12/13
 * Time: 8:32 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Gladtur\MobileBundle\Controller;

use Gladtur\TagBundle\Controller\JsonController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use \Doctrine\Common\Collections\Criteria;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class CategoriesController extends JsonController
{

    /**
     * @Route("subcategories/topcatid/{topcatid}")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Method("GET")
     */
    public function subcategoriesFlatAction($topcatid=0){
        $em = $this->getDoctrine()->getManager();
        $subcategoriesData = array();
       if($topcatid > 0){
          $subcategoriesEntities = $em->getRepository('GladturTagBundle:LocationCategory')->findBy(array('parentCategory'=>$topcatid));
       }
       else{
        $subcategoriesEntities = $em->getRepository('GladturTagBundle:LocationCategory')->findBy(array('isTopcategory'=>!1));
       }
       foreach($subcategoriesEntities as $subcategory){
        $subcategoriesData[] = array('id'=>$subcategory->getId(), 'name' => $subcategory->getName());
       }
    return parent::getJsonForData($subcategoriesData);
    }

    /**
     * @Route("categories/top")
     * @Method("GET")
     */
    public function topcategoriesAction()
    {
        $em = $this->getDoctrine()->getManager();
  //      $topCategoriesEntities = $em->getRepository('GladturTagBundle:LocationCategory')->findBy(array('parentCategory'=>MYSQLI_NOT_NULL_FLAG));
        $topCategoriesEntities = $em->getRepository('GladturTagBundle:LocationCategory')->findBy(array('isTopcategory'=>1));
        $topCategoriesData = array();
        /**
         * @var LocationCategory $topcategory
         */
        foreach($topCategoriesEntities as $topcategory){
            $topCategoriesData[] = array('id'=>$topcategory->getId(), 'name'=>$topcategory->getName());
        }
        return parent::getJsonForData($topCategoriesData);
    }
}