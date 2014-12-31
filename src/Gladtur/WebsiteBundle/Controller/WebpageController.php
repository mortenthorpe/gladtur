<?php
/**
 * Created by PhpStorm.
 * User: mortenthorpe
 * Date: 28/07/14
 * Time: 14.30
 */

namespace Gladtur\WebsiteBundle\Controller;


use Gladtur\WebsiteBundle\Entity\Webpage;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


class WebpageController extends Controller {
  /**
   * @Route("cms/{slug}", name="page_for_slug")
   */
    public function pageForSlugAction($slug){
        /**
         * @var $webpage Webpage
         */
        $webpage = $this->getDoctrine()->getRepository('Gladtur\WebsiteBundle\Entity\Webpage')->findOneBy(array('published'=>true, 'slug' => $slug));
        $pageslots = $webpage->getSlots();
        return $this->render('WebsiteBundle:WebpageTemplates:' . $webpage->getTemplateName()->getTemplateTpl() . '.html.twig',
            array('slots' => $pageslots, 'meta_description' => $webpage->getMetaDescription(), 'meta_keywords' => $webpage->getMetaKeywords(), 'pagetitle' => $webpage->getPagetitle())
        );
    }
} 