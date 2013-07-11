<?php
namespace Gladtur\TagBundle\Controller;

use Gladtur\TagBundle\Entity\UserLocationMedia;
use Gladtur\TagBundle\Form\UserLocationDataType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gladtur\TagBundle\Entity\Location;
use Gladtur\TagBundle\Form\LocationType;
use JMS\Serializer;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 1/13/13
 * Time: 4:10 PM
 * To change this template use File | Settings | File Templates.
 */
/**
 * UserLocationData controller.
 *
 * @Route("location_user_data")
 */
class UserLocationDataController extends Controller
{

    public function __construct()
    {
        //parent::setContainer(new ContainerInterface());
        $this->container = new \Symfony\Component\DependencyInjection\Container();
        // ... deal with any more arguments etc here
    }

    // \@Route("/{id}/{lang}/{file}", requirements={"id" = "\d+"}, defaults={"file" = null})
    /**
     * Lists all UserLocationData entities.
     *
     * @Route("", name="location_user_data", defaults={"orderDir" = 1})
     * @Template()
     */
    public function indexAction($orderDir)
    {
        $menuItems = array('tagIndex' => '@tag', 'location_tagIndex' => '@location_tag', 'locationIndex' =>'@location', 'tag_categoryIndex' => '@tagcategoryIndex' );
        // See: http://stackoverflow.com/questions/12017254/symfony2-how-to-join-on-many-to-many-relations-using-querybuilder //
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('GladturTagBundle:UserLocationData');//->findAll();
        $orderDir=($orderDir==1)?'desc':'asc';
        $query=$entities->createQueryBuilder('user_data')->where('user_data.user='.$this->getUser()->getId())->orderBy('user_data.id',$orderDir)->setMaxResults(1);
        //if($location)$query=$query->where('user_data.location='.$location);
        $query=$query->getQuery();//where('user_data.locationId=')->getQuery();
        $entities=$query->getResult();
        return array(
            'entities' => $entities,
            'menuItems' => $menuItems
        );
    }

    /**
     * Finds and displays a UserLocationData entity.
     *
     * @Route("/{id}/show", name="location_user_data_show")
     * @Template()
     */
    public function showAction($id){

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GladturTagBundle:UserLocationData')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserLocationData entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Finds and displays a UserLocationData entity.
     *
     * @Route("/json/{id}/show", name="location_user_data_json_show")
     * @Template()
     */
    public function showJSONAction($id){
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GladturTagBundle:UserLocationData')->createQueryBuilder('data')->select('data.id', 'data.txt_description')->getQuery()->getResult();//   find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserLocationData entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $serializer = $this->container->get('jms_serializer');
        $response=new \Symfony\Component\HttpFoundation\Response($serializer->serialize($entity, 'json'));
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }
    /* Copy-pasted, auto-edited */
    /**
     * Displays a form to create a new Location entity.
     *
     * @Route("/new", name="location_user_data_new")
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
     * @Route("/create", name="location_user_data_create")

     * @Template("/GladturTagBundle:UserLocationData:new.html.twig")
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

            return $this->redirect($this->generateUrl('location_user_data_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Location entity.
     *
     * @Route("/{id}/edit", name="location_user_data_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('GladturTagBundle:UserLocationData')->find($id);

  /*      $dQuery=$em->createQuery("select d from Gladtur\TagBundle\Entity\UserLocationData d, Gladtur\TagBundle\Entity\User u where u.profile=" . $cur_usr->getProfile()->getId(). " and d.location=".$id." order by d.created_at desc")->setMaxResults(1); -- Fetches UserLocationData results ALSO dependent on user profile! */

        //$entity = $em->getRepository('GladturTagBundle:UserLocationData')->createQueryBuilder('LocationUserDatas')->where('LocationUserDatas.location='.$entity->getId())->orderBy('LocationUserDatas.id', 'desc')->getQuery()->getResult();
      //  $entityUserTagData = $em->getRepository('GladturTagBundle:UserLocationTagData')->createQueryBuilder('LocationUserTagDatas')->where('LocationUserTagDatas.location='.$entity->getLocation()->getId())/*->orderBy('LocationUserTagDatas.user', 'desc')*/->getQuery()->getResult();
        /* http://docs.doctrine-project.org/en/2.0.x/reference/query-builder.html
        // Example - $qb->leftJoin('u.Phonenumbers', 'p', Expr\Join::WITH, $qb->expr()->eq('p.area_code', 55))
        */
        //      $entityUserCategories = $em->getRepository('GladturTagBundle:UserLocationDataCategory')->createQueryBuilder('LocationCategory')->leftJoin('UserLocationData')->where('LocationCategory.')

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Location entity.');
        }


        $entity->addMedia(new UserLocationMedia());
        $editForm = $this->createForm(new UserLocationDataType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'userLocationTagData' => array(),
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Location entity.
     *
     * @Route("/{id}/update", name="location_user_data_update")

     * @Template("/GladturTagBundle:UserLocationData:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GladturTagBundle:UserLocationData')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Location entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new LocationType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('location_user_data_edit', array('id' => $id)));
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
     * @Route("/{id}/delete", name="location_user_data_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('GladturTagBundle:UserLocationData')->find($id);

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
