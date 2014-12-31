<?php
namespace Gladtur\WebsiteBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Proxies\__CG__\Gladtur\TagBundle\Entity\TvguserProfile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Solarium\QueryType\Select\Query\Query as Query;

class SearchController extends Controller{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("search", name="sitesearch")
     */
    public function solrSearchAction(Request $request){
        $userProfile = ($this->getUser() && $this->getUser()->getProfile())?$this->getUser()->getProfile():null;
        $searchTopCategorySlug = 'alle';
        $postedform = $request->get('form', null);
        if(!$postedform) return $this->redirect($this->generateUrl('homepage'));
        $searchQuery=($postedform['query'] && ($postedform['query'] <> ''))?$postedform['query']:null;
        $rawSearchQuery = $searchQuery;
        $searchTopCategoryId = (isset($postedform['category']) && $postedform['category']<>'')?intval($postedform['category']):null;
        if($searchTopCategoryId){
          $searchTopCategory = $this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\LocationCategory')->find($searchTopCategoryId);
          $searchTopCategorySlug = $searchTopCategory->getSlug();
        }
        $lat = $request->get('lat', null);
        $lon = $request->get('lon', null);
        $zoom = $request->get('zoom',6);
        $client = $this->container->get('solarium.client');
        $select = $client->createSelect();
        if($searchQuery){
            // Searching by plain-text, matching the name (place title) field in SOLR index.
            // If the search-text is the AJAX autocomplete form, then parse out the name from this string.
            $commapos = (strpos($searchQuery, ', '))?strpos($searchQuery, ', '):-1;
            if($commapos !== -1 ){
                $searchQuery = mb_substr($searchQuery, 0, $commapos);
            }
            // Post-treat all search-strings for special characters and prepare for SOLR index format.
            $searchQuery = str_replace(array('+', ' ', '&'),array('\+','', '\&'), $searchQuery);
            $searchQuery = str_replace(array('Æ','Ø','Å','æ','ø','å','\u00c6','\u00d8','\u00c5','\u00e6','\u00f8','\u00e5'), array('AE','OE','AA','ae','oe','aa','AE','OE','AA','ae','oe','aa'), $searchQuery);
            $searchQuery = strtolower($searchQuery);
            $searchQuery = trim($searchQuery);
            $searchQuery = urlencode($searchQuery);
            // And pass the final query to SOLR object...
            $select->setQuery('name:*'.$searchQuery.'*');// OR categories:*'.$searchQuery.'*');
        }
        if($searchTopCategoryId){
            // Searching by top category ID in SOLR index.
            $select->setQuery('topcategory_id:'.$searchTopCategoryId);// OR categories:*'.$searchQuery.'*');
        }
        if($lat && $lon){
            $this->get('session')->set('ulat', $lat);
            $this->get('session')->set('ulng', $lon);
            if($this->getUser()){
            $this->getUser()->setLatitude($lat);
            $this->getUser()->setLongitude($lon);
            $this->getDoctrine()->getManager()->persist($this->getUser());
            $this->getDoctrine()->getManager()->flush();
            }
            // Sort by radial distance from reference lat,lon in SOLR, until a distance of $radius
            //Query::SORT_ASC
            $selectHelper = $select->getHelper();
            $radius = 1000; // Distance for radial search in Kilometers, covers Denmark, so OK with 1000.
            $select->createFilterQuery('distance')->setQuery($selectHelper->geofilt('location', $lat, $lon, $radius));
            $select->addSort('geodist(location,'. $lat .','.$lon.')', Query::SORT_ASC);
        }
        $results = $client->select($select);
        $rsString='';
        $locations = array();
        foreach($results as $document){
            foreach($document as $field => $value){
                if(is_array($value)) continue;
                if($field == 'id'){
                    $rsString.='@'.$field.': '.$value.', ';
                }
                if($field == 'id'){
                    //$locationTmp = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\Location', intval($value));
                    $locations[]=intval($value);
                }
            }
        }
        /**
         * @var Session $session
         */
        $session = $this->get('session');

        if(($searchQuery !== 'soegeord' ) /*&& (!isset($_GET['lat']))*/){
            $session->getFlashBag()->add('searchQuery', $searchQuery);
            $session->getFlashBag()->add('rawSearchQuery', $rawSearchQuery);
        }

        if(count($locations)){
            // Go directly to the detail page for single results
            if(count($locations) == 1){
                $locationID = $locations[0];
                $location = $this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\Location')->find($locationID);
                return $this->redirect($this->generateUrl('location_details', array('locationslug' => $location->getSlug())));
            }
            if($rawSearchQuery){
              return $this->redirect($this->generateUrl('findplaces_in_category_named', array('categoryslug' => $searchTopCategorySlug, 'searchterm' => $rawSearchQuery, 'zoom' => $zoom)));
            }
            else{
              return $this->redirect($this->generateUrl('findplaces_in_category', array('categoryslug' => $searchTopCategorySlug, 'zoom' => $zoom)));
            }
        }

            $session->getFlashBag()->add('notice', 'Der er desværre intet, der matcher din søgning.');
            $session->getFlashBag()->add('searchRsEmpty', true);
            return $this->redirect($this->generateUrl('findplace', array('zoom' => $zoom)));
    }

    /**
     * @Route("ajaxsearch", name="ajaxsearch")
     */
    function ajaxsearchAction(Request $request){
        //     $reqQuery = $request->get('form');
        $sQuery = $this->getDoctrine()->getManager()->createQuery("select l.slug, l.readableName, l.addressCity from Gladtur\TagBundle\Entity\Location l where l.published=true and l.readableName like :name")->setMaxResults(10);
        $sQuery->setParameter('name', '%'.$request->get('name').'%');
        //  $sQuery = $this->getDoctrine()->getManager()->createQuery("select l.readableName, l.addressCity from Gladtur\TagBundle\Entity\Location l where l.published=true");
        $sQueryRs = $sQuery->getResult();
        $rsJsonAssoc = array();
        foreach($sQueryRs as $rsAssoc){
            $readableName = htmlspecialchars_decode($rsAssoc['readableName'], true);
            $rsJsonAssoc[] = array('label' => str_replace('&#38;','&',$readableName) . ', ' . $rsAssoc['addressCity'], 'slug' => $rsAssoc['slug']);
        }
        return new JsonResponse($rsJsonAssoc);
        //return new JsonResponse(array(array('label'=>'Morten'), array('label'=>'Thomas')));
    }

    public function searchFormAction(){
        $defaultData = array('query'=>'Søgeord, Bynavn');
        $formView = $this->createFormBuilder()->add('query', 'text', array('required'=>false, 'label'=>false, 'attr' => array('placeholder' => 'Søgeord, Bynavn')))->add('category', 'entity', array('required'=>false, 'class'=>'Gladtur\TagBundle\Entity\LocationCategory', 'multiple'=>false,'expanded'=>true, 'attr'=>array('class'=>'categories_list'), 'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('loccat')->where('loccat.isTopcategory=1')->orderBy('loccat.id', 'ASC');
                }))->getForm()->createView();
        return $this->render(
            'WebsiteBundle:Search:form.html.twig',
            array('searchform' => $formView)
        );
    }
}