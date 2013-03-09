<?php

namespace Gladtur\TagBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gladtur\TagBundle\Entity\LocationTag;
use Gladtur\TagBundle\Form\LocationTagType;

/**
 * LocationTag controller.
 *
 * @Route("locationtag")
 */
class LocationTagController extends Controller
{
    /**
     * Lists all LocationTag entities.
     *
     * @Route("", name="locationtag")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GladturTagBundle:LocationTag')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a LocationTag entity.
     *
     * @Route("/{id}/show", name="locationtag_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GladturTagBundle:LocationTag')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find LocationTag entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new LocationTag entity.
     *
     * @Route("/new", name="locationtag_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new LocationTag();
        $form   = $this->createForm(new LocationTagType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new LocationTag entity.
     *
     * @Route("/create", name="locationtag_create")
     * @Method("POST")
     * @Template("GladturTagBundle:LocationTag:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new LocationTag();
        $form = $this->createForm(new LocationTagType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('locationtag_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing LocationTag entity.
     *
     * @Route("/{id}/edit", name="locationtag_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GladturTagBundle:LocationTag')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find LocationTag entity.');
        }

        $editForm = $this->createForm(new LocationTagType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing LocationTag entity.
     *
     * @Route("/{id}/update", name="locationtag_update")
     * @Method("POST")
     * @Template("GladturTagBundle:LocationTag:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GladturTagBundle:LocationTag')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find LocationTag entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new LocationTagType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('locationtag_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a LocationTag entity.
     *
     * @Route("/{id}/delete", name="locationtag_delete")
     *
     */
    public function deleteAction($id)
    {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GladturTagBundle:LocationTag')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find LocationTag entity.');
            }

            $em->remove($entity);
            $em->flush();

        return $this->redirect($this->generateUrl('locationtag'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
