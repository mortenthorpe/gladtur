<?php

namespace Gladtur\TagBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use
    Symfony\Component\HttpFoundation\File; // Upload with Doctrine Entity Backed data: http://symfony.com/doc/2.0/cookbook/doctrine/file_uploads.html
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gladtur\TagBundle\Entity\TagCategory;
use Gladtur\TagBundle\Form\TagCategoryType;

/**
 * TagCategory controller.
 *
 * @Route("tagcategory")
 */
class TagCategoryController extends Controller
{
    /**
     * Lists all TagCategory entities.
     *
     * @Route("", name="tagcategory")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GladturTagBundle:TagCategory')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a TagCategory entity.
     *
     * @Route("/{id}/show", name="tagcategory_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GladturTagBundle:TagCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TagCategory entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new TagCategory entity.
     *
     * @Route("/new", name="tagcategory_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new TagCategory();
        $form = $this->createForm(new TagCategoryType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new TagCategory entity.
     *
     * @Route("/create", name="tagcategory_create")
     * @Method("POST")
     * @Template("GladturTagBundle:TagCategory:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new TagCategory();
        $form = $this->createForm(new TagCategoryType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tagcategory', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing TagCategory entity.
     *
     * @Route("/{id}/edit", name="tagcategory_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GladturTagBundle:TagCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TagCategory entity.');
        }
        $editForm = $this->createForm(new TagCategoryType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing TagCategory entity.
     *
     * @Route("/{id}/update", name="tagcategory_update")
     * @Method("POST")
     * @Template("GladturTagBundle:TagCategory:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        /*        $document = new Document();
                $editForm = $this->createFormBuilder($document)
                        ->add('iconFilepath')
                        ->add('file')
                        ->getForm();*/

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GladturTagBundle:TagCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TagCategory entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new TagCategoryType(), $entity);
        $eIconFile = new \Symfony\Component\HttpFoundation\File\File($entity->image);
        $fName = 'aFile.png';
        $eIconFile->move(__DIR__ . '/../../../../web/uploads', $fName);
//        $entity->setIconFilepath($entity->iconFilepath->getClientOriginalName());
        $entity->setIconFilepath('/Users/mortenthorpe/sites/symf21/web/uploads/' . $fName);
        $editForm->bind($request);


        if ($editForm->isValid()) {
            $entity->setIconFilepath('/Users/mortenthorpe/sites/symf21/web/uploads/' . $fName);
            $entity->iconFilepath = '/Users/mortenthorpe/sites/symf21/web/uploads/' . $fName;
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tagcategory_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a TagCategory entity.
     *
     * @Route("/{id}/delete", name="tagcategory_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('GladturTagBundle:TagCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TagCategory entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('tagcategory'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm();
    }
}
