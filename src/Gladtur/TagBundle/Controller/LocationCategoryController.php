<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 7/17/13
 * Time: 10:13 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Gladtur\TagBundle\Controller;


use Gladtur\TagBundle\Entity\LocationCategory;
use Gladtur\TagBundle\Form\LocationCategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class LocationCategoryController extends Controller{
    /**
     * Displays a form to create a new LocationCategory entity.
     *
     * @Route("locationcategory/add", name="locationcategory_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new LocationCategory();
        $form = $this->createForm(new LocationCategoryType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("locationcategory/create", name="locationcategory_create")
     */
    public function createAction(Request $request)
    {
        $entity  = new LocationCategory();
        $form = $this->createForm(new LocationCategoryType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $entity->upload($form, 'iconVirtual');
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                $entity->getReadableName().' er oprettet, opret ny nu!'
            );
            return $this->redirect($this->generateUrl('locationcategory_new', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Edits an existing Tag entity.
     *
     * @Route("/{id}/update", name="tag_update")
     * @Method("POST")
     * @Template("GladturTagBundle:Tag:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /**
         * @var LocationCategory $entity
         */
        $entity = $em->getRepository('GladturTagBundle:LocationCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tag entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new LocationCategoryType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $entity->upload($editForm, 'iconVirtual');
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('location_category_edit', array('id' => $id)));
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
     * @Route("/{id}/delete", name="tag_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('GladturTagBundle:LocationCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find LocationCategory entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('tag'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
            ;
    }

}