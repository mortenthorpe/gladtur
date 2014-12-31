<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 6/10/13
 * Time: 9:25 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Gladtur\MobileBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Gladtur\TagBundle\Entity\TvguserProfile;
use Gladtur\TagBundle\Form\TvguserProfileType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Gladtur\TagBundle\Controller\JsonController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class ProfilesController extends JsonController
{
    /**
     * Lists all Profiles entities.
     *
     * @Route("uprofiles", name="list_profiles")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        //$entities = $em->getRepository('GladturTagBundle:TvguserProfile')->createQueryBuilder('TvguserProfile')->getQuery()->getResult();
        /** @var EntityRepository $entities */
        $entities = $em->getRepository('GladturTagBundle:TvguserProfile')->findBy(array(), array('rank'=>'ASC'));
        $data = array(); /* entities gready-loads all related objects, so we need to filter out the unneeded  */
        /**
         * @var TvguserProfile $entity
         */
        foreach ($entities as $entity) {
            $entData = array();
            $entData['id'] = $entity->getId();
            $entData['name'] = $entity->getReadableName();
            $entData['info'] = $entity->getTxtDescription();
            $entData['icon'] = 'http://gladtur.dk'.'/uploads/avalanche/thumbnail/icons/profiles/'.$entity->getPath();
            $entData['individualized'] = ($entity->getIndividualized())?1:0;
            $data[] = $entData;
        }
        return parent::getJsonForData($data);
    }

    /**
     * @Route("uprofile/{id}")
     * @Method("GET")
     */
    public function profiledetailAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var EntityRepository $profile */
        $profile = $em->getRepository('GladturTagBundle:TvguserProfile')->find($id);
        $profileData = array();
        if ($profile) {
            $profileData = array(
                'items' => array(
                    array(
                        'id' => $profile->getId(),
                        'name' => $profile->getReadableName(),
                        'info' => $profile->getTxtDescription(),
                        'value' => 2
                    ),
                    array(
                        'id' => $profile->getId() + mt_rand(1, 1000),
                        'name' => 'Skiltning for svagt seende',
                        'info' => 'Some info for item 2!',
                        'value' => 1
                    )
                )
            );
        }

        return parent::getJsonForData($profileData);
    }

    public function editAction(Request $request)
    {
        $editForm = $this->createForm($formType, $entity, array('csrf_protection' => false));
    }

    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GladturTagBundle:TvguserProfile')->find($id);
        $form_options = array();
        if (parent::getIsJSON()) {
            $form_options = array('csrf_token' => false);
        }
        $edit_form = $this->createForm(new TvguserProfileType(), $entity, $form_options);

        return array(
            'entity' => $entity,
            'form' => $edit_form->createView(),
        );
    }
}