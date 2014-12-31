<?php

namespace Gladtur\TagBundle\Controller;

use Gladtur\WebsiteBundle\Form\LocationPublicType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gladtur\TagBundle\Entity\Location;
use Gladtur\TagBundle\Form\LocationType;
use Ivory\GoogleMap\Overlays\Animation;
use Ivory\GoogleMap\Overlays\Marker;
use Doctrine\ORM\Tools\Pagination\Paginator;

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
     * @Route("/start/{start}/sortby/{sortby}", name="location_paged")
     * @Route("/start/{start}/sortby/{sortby}/dir/{sortdir}", name="location_paged_directed")
     * @Template()
     */
    public function indexAction($start = 0, $sortby='id', $sortdir='ASC')
    {
		$menuItems = array('tagIndex' => '@tag', 'location_tagIndex' => '@location_tag', 'locationIndex' =>'@location', 'tag_categoryIndex' => '@tagcategoryIndex' );
        //$em = $this->getDoctrine()->getManager();

        /*
         * use Doctrine\ORM\Tools\Pagination\Paginator;

$dql = "SELECT p, c FROM BlogPost p JOIN p.comments c";
$query = $entityManager->createQuery($dql)
                       ->setFirstResult(0)
                       ->setMaxResults(100);

$paginator = new Paginator($query, $fetchJoinCollection = true);

$c = count($paginator);
foreach ($paginator as $post) {
    echo $post->getHeadline() . "\n";
}

         */
 //       $entities = $em->getRepository('GladturTagBundle:Location')->findAll();

        $rows = 100;
        $dql = "SELECT l FROM Gladtur\TagBundle\Entity\Location l where l.slug is not null and l.slug !='' order by l.".$sortby." ".$sortdir;
        $query = $this->getDoctrine()->getManager()->createQuery($dql)
            ->setFirstResult($start)
            ->setMaxResults($rows);

        $paginator = new Paginator($query, false);

        return array(
            'entities' => $paginator,
            'prev_start'=>$start-$rows,
            'next_start'=>$start+$rows,
            'next_pageno' => ($start/$rows)+2,
            'prev_pageno' => ($start/$rows),
            'cur_pageno' => ($start/$rows)+1,
            'start' => $start,
            'rows' => $rows,
            'sortby'=>$sortby,
            'sortdir' => $sortdir,
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
        $entity->setUserLocationData(new UserLocationData());
        $form = $this->createForm(new LocationType(), $entity);
        $form->remove('userData');
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
        $entityUserDatasFull = $em->getRepository('GladturTagBundle:UserLocationData')->createQueryBuilder('LocationUserDatas')->where('LocationUserDatas.location='.$entity->getId())->orderBy('LocationUserDatas.created_at', 'desc')->getQuery()->getResult();

        $entityUserData=$em->createQuery("select d from Gladtur\TagBundle\Entity\UserLocationData d, Gladtur\TagBundle\Entity\User u where u.profile=" . $this->getUser()->getProfile()->getId(). " and d.location=".$id." order by d.created_at desc")->setMaxResults(1)->getSingleResult();

      //  $entityUserTagData = $em->getRepository('GladturTagBundle:UserLocationTagData')->createQueryBuilder('LocationUserTagDatas')->where('LocationUserTagDatas.location='.$entity->getId())->orderBy('LocationUserTagDatas.user', 'desc')->getQuery()->getResult();
        /* http://docs.doctrine-project.org/en/2.0.x/reference/query-builder.html
        // Example - $qb->leftJoin('u.Phonenumbers', 'p', Expr\Join::WITH, $qb->expr()->eq('p.area_code', 55))
        */
  //      $entityUserCategories = $em->getRepository('GladturTagBundle:LocationCategory')->createQueryBuilder('LocationCategory')->leftJoin('UserLocationData')->where('LocationCategory.')

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Location entity.');
        }

        $editForm = $this->createForm(new LocationPublicType(), $entity);
        $deleteForm = $this->createDeleteForm($id);
        $map = $this->get('ivory_google_map.map');
        $map->setLanguage('da');
        $latlong=$this->getLatLongGeoLoc($entity);

        $marker = new Marker();

// Configure your marker options
        $marker->setPrefixJavascriptVariable('marker_');
        $marker->setPosition($latlong['lat'], $latlong['lng'], true);
        $marker->setAnimation(Animation::DROP);
        $marker->setOptions(array(
                'clickable' => false,
                'flat'      => true,
            ));
        $map->addMarker($marker);
        $map->setCenter($latlong['lat'], $latlong['lng'], true);
        $map->setStylesheetOption('width', '640px');
        $map->setStylesheetOption('height', '300px');
        $map->setAutoZoom(true);
        $map->setBound($latlong['lat']-0.005, $latlong['lng']-0.005, $latlong['lat']+0.005, $latlong['lng']+0.005, true, true);
        return array(
            'entity'      => $entity,
            'userLocationAllData' => $entityUserDatasFull,
            'userLocationData' => $entityUserData,
            //'userLocationTagData' => $entityUserTagData,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'geolocation' => $latlong,
            'map' => $map,
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
        $editForm = $this->createForm(new LocationPublicType(), $entity);
        $editForm->remove('userData');
        $editForm->bind($request);
/**        $userManager = $this->get('fos_user.user_manager');  
        $creatingUser = $userManager->findUserBy(array('id' => 2)); WORKS! */

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
            $entity->setPublished(false);
            $em->persist($entity);
            $em->flush();
            $solrClient = $this->get('solarium.client');
            $update = $solrClient->createUpdate();
            $update->addDeleteQuery('id:'.$entity->getId());
            $update->addCommit();
            $rs = $solrClient->update($update);
            return $this->redirect($this->generateUrl('location'));
    }


    /**
     * @Route("/{id}/revive", name="location_revive")
     */
    public function reviveAction($id){
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('GladturTagBundle:Location')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Location entity.');
        }
        $entity->setPublished(true);
        $em->persist($entity);
        $em->flush();
        $solrClient = $this->get('solarium.client');
        $update = $solrClient->createUpdate();
        $update->addDocument($entity->toSolariumDocument($solrClient));
        $update->addCommit();
        $rs = $solrClient->update($update);
        return $this->redirect($this->generateUrl('location'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    public function getLatLongGeoLoc(&$entity = null){
        /* Google Vendor Bundle GeoLocation service called */
        $geolocationApi = $this->get('google_geolocation.geolocation_api');
        // Offline quick fix:
        //return array('lat'=>'55.6', 'lng' => '12.6');
        if($entity){
            $location = $geolocationApi->locateAddress(implode(', ',array($entity->getAddressStreet(), $entity->getAddressCity(), $entity->getAddressCountry())));
        }

        if ($location && $location->getMatches() > 0)
        {
            $matches = json_decode($location->getResult(), true);

            // Get address components [city, country, postcode, etc] for 1st match
            $components = $location->getAddressComponents(0);

            // Get LatLng for first match
            $latLng = $location->getLatLng(0);
        }

        return $latLng;
    }
}
