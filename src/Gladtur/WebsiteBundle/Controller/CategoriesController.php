<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 8/4/13
 * Time: 12:39 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Gladtur\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class CategoriesController extends Controller{
    /**
     * @Template("WebsiteBundle:Login:categories.html.twig")
     */
    public function listAction(){
        $categories = $this->getDoctrine()->getManager()->getRepository('Gladtur\TagBundle\Entity\LocationCategory')->findBy(array('isTopcategory'=>1), array('id'=>'ASC'));
        return array(
            'categories' => $categories,
        );
    }

    /**
     * @Route("subcategories/{topcatid}")
     */
    public function subcategoriesAction($topcatid){
        $subcategoriesEntities = $this->getDoctrine()->getManager()->getRepository('GladturTagBundle:LocationCategory')->findBy(array('parentCategory'=>$topcatid));
        $subcategories = array();
        foreach($subcategoriesEntities as $category){
            /**
             * LocationCategory $category
             */
            $subcategories[] = 'gladturlocation_locationCategories_'.$category->getId();
        }
        return new JsonResponse(array('subcategories'=>$subcategories));
    }
}