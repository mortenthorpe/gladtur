<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 7/19/13
 * Time: 3:14 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Gladtur\MobileBundle\Controller;

use Doctrine\ORM\EntityManager;
use Gladtur\TagBundle\Controller\JsonController;
use Gladtur\TagBundle\Entity\ReportedItem;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class ReportItemsController extends JsonController{
        /**
         * @Route("reportkind")
         * @Method("POST")
         * Expects JSON-array with required data: {'foreign_key_id':[0-9]+,'kind':'comment'(0)/'location'(1), 'report_txt':''}
         */
        public function reportPlaceOrCommentAction(Request $request){
            if(parent::getIsJSON()){
                if(parent::getTokenPassed($request)){
                    $reqAssoc = parent::getRequestFromJSON($request);
                    $reportedItem = new ReportedItem();
                    $reportedItem->setUser(parent::getUser());
                    $reportedItem->setUserProfile(parent::getUser()->getProfile());
                    $reportedItem->setKindID(($reqAssoc['kind']=='comment')?0:1);
                    $reportedItem->setForeignKeyId(intval($reqAssoc['foreign_key_id']));
                    $reportedItem->setUserReportTxt($reqAssoc['report_txt']);
                    $this->getDoctrine()->getManager()->persist($reportedItem);
                    $this->getDoctrine()->getManager()->flush();

                    return parent::getJsonForData(array('success' => 1, 'msg'=>$this->get('translator')->trans('gladtur_report_success'))); //Tak for din anmeldelse. Vi gennemser din kommentar og følger snarest muligt op på sagen.
                }
            return parent::getJsonForData(array('success' => 0, 'msg' => $this->get('translator')->trans('gladtur_report_error'))); //Der opstod en fejl i afsending af din kommentar, prøv venligst igen eller senere.
        }
        }
}