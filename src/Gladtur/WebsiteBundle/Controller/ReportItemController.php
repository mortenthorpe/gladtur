<?php
/**
 * Created by PhpStorm.
 * User: mortenthorpe
 * Date: 29/01/14
 * Time: 12.29
 */

namespace Gladtur\WebsiteBundle\Controller;

use Doctrine\ORM\EntityManager;
use Gladtur\TagBundle\Entity\ReportedItem;
use Gladtur\WebsiteBundle\Form\Type\ReportedItemType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ReportItemController extends Controller{

    /**
     * @Route("indberet/sted/{slug}", name="location_report")
     * @Template("WebsiteBundle:ReportItems:report_form.html.twig")
     */
    public function reportItemFormAction($slug, Request $request){
        if(!$slug){
            throw $this->createNotFoundException('Stedet findes ikke!');
        }
        $location = $this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\Location')->findOneBy(array('slug' => $slug));
        if(!$location){
            throw $this->createNotFoundException('Stedet findes ikke!');
        }
        $this->get('session')->set('referrer_target_path', $this->generateUrl('location_details', array('locationslug'=>$slug,'action' => 'editReportLocation')));
        $reportForm = $this->createForm(new ReportedItemType(), new ReportedItem());
        return array(
            'report_form' => $reportForm->createView(),
            'slug' => $slug,
        );
    }

    /**
     * @Route("indberet/gem", name="location_report_save")
     * @Method("POST")
     */
    public function reportItemAction(Request $request){
        $postedData = $request->get('reported_item');
        if(!$request->get('slug', false)){
          throw $this->createNotFoundException('Stedet findes ikke! Slug ikke sat!');
        }
        $location = $this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\Location')->findOneBy(array('slug' => $request->get('slug')));
        if(!$location){
            throw $this->createNotFoundException('Stedet findes ikke! pga stedet');
        }
        $reportedItem = new ReportedItem();
        $reportedItem->setUser($this->getUser());
        $reportedItem->setUserProfile($this->getUser()->getProfile());
        $reportedItem->setKindID(1);// 0: comment, 1:location(current use)
        $reportedItem->setForeignKeyId($location->getId());
        $reportedItemForm = $this->createForm(new ReportedItemType(), $reportedItem);
        $reportedItemForm->bind($postedData);
        if($reportedItemForm->isValid()){
            $this->getDoctrine()->getManager()->persist($reportedItem);
            $this->getDoctrine()->getManager()->flush();
        }
        return new Response($this->get('translator')->trans('gladtur_report_success'));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("sted/{locationslug}/redirect/reportlocation", name="location_reportlocation_redirectregister");
     */
    public function reportItemRegisterTargetPath($locationslug){
        $this->get('session')->set('referrer_target_path', $this->generateUrl('location_details', array('locationslug'=>$locationslug,'action' => 'editReportLocation')));
        return $this->redirect($this->generateUrl('fos_user_registration_register'));
    }
} 