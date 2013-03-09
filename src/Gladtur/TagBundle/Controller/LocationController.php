<?php

namespace Gladtur\TagBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gladtur\TagBundle\Entity\Location;
use Gladtur\TagBundle\Form\LocationType;

/**
 * Location controller.
 *
 * @Route("location")
 */
class LocationController extends Controller
{
    /**
     * Lists all Location entities.
     *
     * @Route("", name="location")
     * @Template()
     */
    public function indexAction()
    {
		$menuItems = array('tagIndex' => '@tag', 'location_tagIndex' => '@location_tag', 'locationIndex' =>'@location', 'tag_categoryIndex' => '@tagcategoryIndex' );
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GladturTagBundle:Location')->findAll();

        return array(
            'entities' => $entities,
			'menuItems' => $menuItems
        );

    }

    /**
     * Finds and displays a Location entity.
     *
     * @Route("/{id}/show", name="location_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GladturTagBundle:Location')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Location entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Location entity.
     *
     * @Route("/new", name="location_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Location();
        $form   = $this->createForm(new LocationType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Location entity.
     *
     * @Route("/create", name="location_create")
     
     * @Template("/GladturTagBundle:Location:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Location();
        $form = $this->createForm(new LocationType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('location_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Location entity.
     *
     * @Route("/{id}/edit", name="location_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GladturTagBundle:Location')->find($id);
        $entityUserData = $em->getRepository('GladturTagBundle:UserLocationData')->createQueryBuilder('LocationUserDatas')->where('LocationUserDatas.location='.$entity->getId())->orderBy('LocationUserDatas.id', 'desc')->getQuery()->getResult();
        $entityUserTagData = $em->getRepository('GladturTagBundle:UserLocationTagData')->createQueryBuilder('LocationUserTagDatas')->where('LocationUserTagDatas.location='.$entity->getId())->orderBy('LocationUserTagDatas.user', 'desc')->getQuery()->getResult();
        /* http://docs.doctrine-project.org/en/2.0.x/reference/query-builder.html
        // Example - $qb->leftJoin('u.Phonenumbers', 'p', Expr\Join::WITH, $qb->expr()->eq('p.area_code', 55))
        */
  //      $entityUserCategories = $em->getRepository('GladturTagBundle:LocationCategory')->createQueryBuilder('LocationCategory')->leftJoin('UserLocationData')->where('LocationCategory.')

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Location entity.');
        }

        $editForm = $this->createForm(new LocationType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'userLocationData' => $entityUserData,
            'userLocationTagData' => $entityUserTagData,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Location entity.
     *
     * @Route("/{id}/update", name="location_update")
     
     * @Template("/GladturTagBundle:Location:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GladturTagBundle:Location')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Location entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new LocationType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('location_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Location entity.
     *
     * @Route("/{id}/delete", name="location_delete")
     */
    public function deleteAction($id)
    {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GladturTagBundle:Location')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Location entity.');
            }

            $em->remove($entity);
            $em->flush();

        return $this->redirect($this->generateUrl('location'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
