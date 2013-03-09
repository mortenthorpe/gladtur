<?php

namespace Gladtur\TagBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gladtur\TagBundle\Entity\TvguserProfile;
use Gladtur\TagBundle\Form\TvguserProfileType;

/**
 * TvguserProfile controller.
 *
 * @Route("userprofile")
 */
class TvguserProfileController extends Controller
{
    /**
     * Lists all tvguserprofile entities.
     *
     * @Route("", name="tvguserprofile")
     * @Template()
     */
    public function indexAction()
    {
		
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GladturTagBundle:TvguserProfile')->findAll();


        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a tvguserprofile entity.
     *
     * @Route("/{id}/show", name="tvguserprofile_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GladturTagBundle:TvguserProfile')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TvguserProfile entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new TvguserProfile entity.
     *
     * @Route("/new", name="tvguserprofile_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new TvguserProfile();
        $form   = $this->createForm(new TvguserProfileType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new TvguserProfile entity.
     *
     * @Route("/create", name="tvguserprofile_create")
     * @Method("POST")
     * @Template("GladturTagBundle:TvguserProfile:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new TvguserProfile();
        $form = $this->createForm(new TvguserProfileType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tvguserprofile_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing tvguserprofile entity.
     *
     * @Route("/{id}/edit", name="tvguserprofile_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GladturTagBundle:TvguserProfile')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TvguserProfile entity.');
        }

        $editForm = $this->createForm(new TvguserProfileType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Tag entity.
     *
     * @Route("/{id}/update", name="tvguserprofile_update")
     * @Method("POST")
     * @Template("GladturTagBundle:TvguserProfile:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GladturTagBundle:TvguserProfile')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tag entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new TvguserProfileType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tvguserprofile_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Tag entity.
     *
     * @Route("/{id}/delete", name="tvguserprofile_delete")
     */
    public function deleteAction($id)
    {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GladturTagBundle:TvguserProfile')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find TvguserProfile entity.');
            }

            $em->remove($entity);
            $em->flush();

        return $this->redirect($this->generateUrl('tvguserprofile'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
