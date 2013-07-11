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

class CategoriesController extends JsonController
{

    /**
     * @Route("categories")
     */
    public function indexAction()
    {
        /** @var EntityManager $em */
        /*$em = $this->getDoctrine()->getManager();
        //$entities = $em->getRepository('GladturTagBundle:TvguserProfile')->createQueryBuilder('TvguserProfile')->getQuery()->getResult();
        /** @var EntityRepository $entities */
        /*$childcategoriesCriteria=Criteria::create()->where(Criteria::expr()->eq("parentCategory", null))->orderBy(array('id'));
        $entities = $em->getRepository('GladturTagBundle:LocationCategory')->findAll();//matching($childcategoriesCriteria);
        return parent::getJsonForData($entities);*/
        $categoriesData = array(
            array(
                'id' => 10,
                'name' => 'Mad og spise',
                'subcategories' => array(
                    array('id' => 1, 'name' => 'Restaurant'),
                    array('id' => 2, 'name' => 'Butik'),
                    array('id' => 3, 'name' => 'Bibliotek'),
                    array('id' => 4, 'name' => 'Subcategor4 ID=4 to topcategory ID=10')
                )
            )
        );

        return parent::getJsonForData($categoriesData);
    }

    /**
     * @Route("subcategories")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function subcategoriesFlatAction(){
       $em = $this->getDoctrine()->getManager();
       $subcategoriesData = array();
       //$subcategoriesEntities = $em->getRepository('GladturTagBundle:LocationCategory')->findBy(array('parentCategory'=>MYSQLI_NOT_NULL_FLAG));
       $subcategoriesEntities = $em->getRepository('GladturTagBundle:LocationCategory')->findBy(array('parentCategory'=>null));
       foreach($subcategoriesEntities as $subcategory){
           $subcategoriesData[] = array('id'=>$subcategory->getId(), 'name' => $subcategory->getName());
       }
       if(empty($subcategoriesData)){
            $subcategoriesData = array(
                array('id' => 1, 'name' => 'Restaurant'),
                array('id' => 2, 'name' => 'Butik'),
                array('id' => 3, 'name' => 'Bibliotek'),
                array('id' => 4, 'name' => 'Subcategor4')
            );
       }
    return parent::getJsonForData($subcategoriesData);
    }

    /**
     * @Route("categories/top")
     */
    public function topcategoriesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $topCategoriesEntities = $em->getRepository('GladturTagBundle:LocationCategory')->findBy(array('parentCategory'=>MYSQLI_NOT_NULL_FLAG));
        $topCategoriesData = array();
        /**
         * @var LocationCategory $topcategory
         */
        foreach($topCategoriesEntities as $topcategory){
            $topCategoriesData[] = array('id'=>$topcategory->getId(), 'name'=>$topcategory->getName());
        }
        if(empty($topCategoriesData)){
            $topCategoriesData = array(
                array('id' => 10, 'name' => 'A topcategory ID=10!'),
                array('id' => 99, 'name' => 'A topcategory ID=99')
            );
        }
        return parent::getJsonForData($topCategoriesData);
    }
}