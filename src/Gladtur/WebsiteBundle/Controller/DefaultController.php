<?php

namespace Gladtur\WebsiteBundle\Controller;

use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Gladtur\TagBundle\Controller\GladTurStats;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Ivory\GoogleMap\Places\AutocompleteType;
use Solarium\QueryType\Select\Query\Query as Query;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Gladtur\TagBundle\Controller\loggableParam;

class DefaultController extends Controller
{
    public function exceptionAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null, $format = 'html')
    {
        return $this->render(
            'WebsiteBlogBundle:Default:error.html.twig'
           // array('blogs' => $blogs)
        );
    }

    /**
     * @Route("moved_ajaxsearch", name="ajaxsearch")
     */
    function ajaxsearchTestAction(Request $request){
   //     $reqQuery = $request->get('form');
        $sQuery = $this->getDoctrine()->getManager()->createQuery("select l.readableName, l.addressCity from Gladtur\TagBundle\Entity\Location l where l.published=true and l.readableName like :name");
        $sQuery->setParameter('name', '%'.$request->get('name').'%');
      //  $sQuery = $this->getDoctrine()->getManager()->createQuery("select l.readableName, l.addressCity from Gladtur\TagBundle\Entity\Location l where l.published=true");
        $sQueryRs = $sQuery->getResult();
        $rsJsonAssoc = array();
        foreach($sQueryRs as $rsAssoc){
            $rsJsonAssoc[] = array('label' => $rsAssoc['readableName'] . ', ' . $rsAssoc['addressCity']);
        }
        return new JsonResponse($rsJsonAssoc);
        //return new JsonResponse(array(array('label'=>'Morten'), array('label'=>'Thomas')));
    }
    /**
     * @Route("steder", name="findplace")
     * @Template("WebsiteBundle:Default:index.html.twig")
     */
    public function findplaceAction(Request $request)
    {
        if($this->getUser()){
            $profile = $this->getUser()->getProfile();
        }
        if(!$this->getUser() && !$this->get('session')->get('pid',null)){
            $this->get('session')->set('pid', 3);
        }

        // Rådhuspladsen GetCoords: 55.675283,12.570163
        $lat = floatval($this->get('session')->get('ulat', 55.675283));
        $lon = floatval($this->get('session')->get('ulng', 12.570163));
        /*$this->get('session')->set('ulat', $lat);
        $this->get('session')->set('ulng', $lon);*/
        $pageno = $request->get('side', 0);
        $searchTerm = $request->get('soeg',null);
        $searchCategorySlug = $request->get('kategori',null);
        $radius = abs($request->get('afstand', 20)); // Distance for radial search in Kilometers
        $zoom = intval(abs($this->getRequest()->get('zoom', 10)));
        if(abs($radius) > 1000){
            $radius = 1000;
        }
        $searchCategoryId = null;
        if($searchCategorySlug){
            $searchCategory = $this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\LocationCategory')->findOneBy(array('slug' => $searchCategorySlug));
            if($searchCategory){
                $searchCategoryId = $searchCategory->getId();
            }
        }
        // Sessions: http://stackoverflow.com/questions/8399389/accessing-session-from-twig-template
        //$citiesZipcodes_service = $this->get('gladtur.website.cities');
        $locations = array();
        /*if($request->get('steder', null)){
            $locationIds = explode(',',$request->get('steder'));
            foreach($locationIds as $locationId){
                $locationTmp =  $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\Location', $locationId);
                if($locationTmp){
                    $locations[] = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\Location', $locationId);
                }
            }
        }*/
        $topCount = null;
            $client = $this->container->get('solarium.client');
            $select = $client->createSelect();
            $select->setStart(0);
            $perpage = 20;
            if($pageno==0){
              $select->setRows($perpage);
              $count_shown = $perpage;
            }
            else{
              $count_shown = 20*($pageno+1);
              $select->setRows($count_shown);
            }
                //$select->setQuery('name:*');
                if($searchCategoryId){
                  $select->setQuery('topcategory_id:' . $searchCategoryId . ' OR subcategory_ids:'.$searchCategoryId);
                }
                if($searchTerm){
                    $searchQuery = str_replace(array('+', ' ', '&'),array('\+','', '\&'), $searchTerm);
                    $searchQuery = str_replace(array('Æ','Ø','Å','æ','ø','å','\u00c6','\u00d8','\u00c5','\u00e6','\u00f8','\u00e5'), array('AE','OE','AA','ae','oe','aa','AE','OE','AA','ae','oe','aa'), $searchQuery);
                    $searchQuery = strtolower($searchQuery);
                    $searchQuery = trim($searchQuery);
                    $select->setQuery('name:*' . $searchQuery .'*');
                }
                if($searchCategoryId && $searchTerm){
                    $select->setQuery('topcategory_id:' . $searchCategoryId . ' AND name:' . mb_strtolower($searchTerm) .'*');
                }
        /*        $lat = floatval($this->get('session')->get('ulat'));
                $lon = floatval($this->get('session')->get('ulng'));*/
                // Sort by radial distance from reference lat,lon in SOLR, until a distance of $radius
                $selectHelper = $select->getHelper();
                $select->createFilterQuery('distance')->setQuery($selectHelper->geofilt('location', $lat, $lon, $radius));
                //$select->createFilterQuery('distance')->setQuery('{!func}'.$selectHelper->geodist('location', $lat, $lon));
                $select->addSort('geodist(location,'. $lat .','.$lon.')', Query::SORT_ASC);
                $select->setFields(array('id'));
            $results = $client->select($select);
            $resultcount = $results->getNumFound();
            if($count_shown > $resultcount){
                $count_shown = $resultcount;
            }
            $rsString='';
            $locations = array();
            $locationids = array();
            foreach($results as $document){
                foreach($document as $field => $value){
                    if(is_array($value)) continue;
                    if($field == 'id'){
                        $rsString.='@'.$field.': '.$value.', ';
                    }
                    if($field == 'id'){
                        //$locationTmp = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\Location', intval($value));
                        $locationids[]=intval($value);
                    }
                }
            }
            foreach($locationids as $locationId) {
               $location = $this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\Location')->find($locationId);
               if($location){
                   $locations[] = $location;
               }
            }
            $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
  /*      $locationsUnordered = $qb->select("l")->from('Gladtur\TagBundle\Entity\Location', 'l')->where('l.published=1')->add('where', $qb->expr()->in('l.id',':location_ids'))->setParameter('location_ids', $locationids)->getQuery()->getResult();*/
        $map = $this->get('ivory_google_map.map');
        $map->setCenter($lat, $lon);
        $map->setLanguage('da');
        $map->setMapOption('zoom',$zoom);
        $map->setStylesheetOption('width', '630px');
        $request->setTrustedProxies(array('127.0.0.1'));
        return array(
            'locations' => $locations,
            'locations_count' => $resultcount,
            'perpage' => $perpage,
            'count_shown' => $count_shown,
            'pageno' => $pageno,
            'search_term' => $searchTerm,
            'search_radius' => $radius,
            'search_categoryslug' => $searchCategorySlug,
            'category' => null,
            'map' => $map,
            'pagetitle' => 'Steder nær dig',
            'htmltitle' => false,
            'slug' => 'steder',
            'zoom' => $zoom,
        );
    }

    /**
    * @Route("steder/{categoryslug}", name="findplaces_in_category", defaults={"categoryslug"="alle"})
    **/
    public function findplacesInCategoryAction($categoryslug){
        $pageno = $this->getRequest()->get('side', 0);
        return $this->forward('WebsiteBundle:Default:findplace', array('kategori' => $categoryslug, 'side' => $pageno, 'zoom'=>$this->getRequest()->get('zoom', 10)));
    }

    /**
    * @Route("steder/{categoryslug}/{searchterm}", name="findplaces_in_category_named", defaults={"categoryslug"="alle", "searchterm" = ""})
    **/
    public function findplacesWithNameInCategoryAction($categoryslug, $searchterm){
        $pageno = $this->getRequest()->get('side', 0);
        return $this->forward('WebsiteBundle:Default:findplace', array('kategori' => $categoryslug, 'soeg'=>$searchterm, 'side' => $pageno, 'zoom'=>$this->getRequest()->get('zoom', 10)));
    }
    /**
     * @Route("", name="homepage")
     * @Route("side/{pagename}", name="websitestatic_page")
     */
    public function sideAction($pagename = 'forsiden')
    {
        if($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            $webpage = $this->getDoctrine()->getRepository('Gladtur\WebsiteBundle\Entity\Webpage')->findOneBy(array('published'=>true, 'slug' => $pagename));
            $response = $this->render('WebsiteBundle:WebpageTemplates:' . $webpage->getTemplateName()->getTemplateTpl() . '.html.twig',
                array('page_id'=>$webpage->getId(), 'pagename' => $pagename, 'meta_description' => $webpage->getMetaDescription(), 'meta_keywords' => $webpage->getMetaKeywords(), 'pagetitle' => $webpage->getPagetitle())
            );
            return $response;
        }
        else{
            $apcCache = new ApcCache();
            if(!$apcCache->contains('websitepage_'.$pagename)){
                $webpage = $this->getDoctrine()->getRepository('Gladtur\WebsiteBundle\Entity\Webpage')->findOneBy(array('published'=>true, 'slug' => $pagename));
                $response = $this->render('WebsiteBundle:WebpageTemplates:' . $webpage->getTemplateName()->getTemplateTpl() . '.html.twig',
                    array('page_id'=>$webpage->getId(), 'pagename' => $pagename, 'meta_description' => $webpage->getMetaDescription(), 'meta_keywords' => $webpage->getMetaKeywords(), 'pagetitle' => $webpage->getPagetitle())
                );
                $apcCache->save('websitepage_'.$pagename, $response, 365*86400);
            }
            else{
                $response = $apcCache->fetch('websitepage_'.$pagename);
            }
        }
        return $response;
    }

    /**
     * @Route("_side/saveslot", name="websitestatic_page_saveslot")
     * @Secure({"ROLE_ADMIN"})
     */
    public function websitestatic_page_saveslot(){
        $apcCache = new ApcCache();
        $apcCache->deleteAll();
        $request = $this->getRequest();
        $slotNameAndId = $request->get('id',null);
        $slotId = substr($slotNameAndId, strpos($slotNameAndId, '_')+1);
        $pageURL = $request->get('page',null);
        if(!$slotId || !$pageURL) return new Response('You cannot use this function in this manner!', '404');
        $slotContent = $request->get('text' ,'');
        $slotObj = $this->getDoctrine()->getRepository('Gladtur\WebsiteBundle\Entity\WebpageSlot')->find($slotId);
        $slotObj->setHtml($slotContent);
        $this->getDoctrine()->getManager()->persist($slotObj);
        $this->getDoctrine()->getManager()->flush($slotObj);
        return new Response('');
    }

    /**
     */
    public function slotForSlugAndPositionAction($slug, $position, $slotclasses = array(),$containerclasses = array()){
        $slotsCritArray = array('published' => 1, 'slug' => $slug, 'block_position' => $position);
        if($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            if($this->get('security.context')->getToken()->getUser() && $this->get('security.context')->getToken()->getUser()->hasRole('ROLE_ADMIN')){
                $slotsCritArray = array('slug' => $slug, 'block_position' => $position);
            }
        }
        $slots = $this->getDoctrine()->getRepository('Gladtur\WebsiteBundle\Entity\WebpageSlot')->findBy($slotsCritArray);
        $htmlOut = '';
        foreach($slots as $slot){
         $htmlOut .= $this->renderView('WebsiteBundle:Default:slot.html.twig',
                array('slot' => $slot, 'containerclasses'=>implode(' ', $containerclasses), 'slotclasses' => implode(' ', $slotclasses))
            );
        }
        return new Response($htmlOut);
    }


    public function slotsForPageAndPositionAction($pageid, $position, $slotclasses = array(),$containerclasses = array()){
        $page = $this->getDoctrine()->getRepository('Gladtur\WebsiteBundle\Entity\Webpage')->find($pageid);
        $apcCache = new ApcCache();
        if(!$apcCache->contains($pageid.$position)){
        $slots = $page->getSlotsAtBlockPosition($position);
        $apcCache->save($pageid.$position, $slots, 86400);
        }
        else{
            $slots = $apcCache->fetch($pageid.$position);
        }
        return $this->render('WebsiteBundle:Default:slots_collection.html.twig',
            array('slots' => $slots, 'containerclasses'=>implode(' ', $containerclasses), 'slotclasses' => implode(' ', $slotclasses))
        );
    }


    public function slotsForSlugAndPositionAction($slug, $position, $slotclasses = array(),$containerclasses = array()){
        $slotsCritArray = array('published' => 1, 'slug' => $slug, 'block_position' => $position);
        if($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            if($this->get('security.context')->getToken()->getUser() && $this->get('security.context')->getToken()->getUser()->hasRole('ROLE_ADMIN')){
                $slotsCritArray = array('slug' => $slug, 'block_position' => $position);
            }
        }
        $slots = $this->getDoctrine()->getRepository('Gladtur\WebsiteBundle\Entity\WebpageSlot')->findBy($slotsCritArray);
        return $this->render('WebsiteBundle:Default:slots_collection.html.twig',
            array('slots' => $slots, 'containerclasses'=>implode(' ', $containerclasses), 'slotclasses' => implode(' ', $slotclasses))
        );
    }

    public function globalSlotsForPositionAction($position, $slotclasses = array(), $containerclasses = array()){
        $apcCache = new ApcCache();
        if(!$apcCache->contains('global_'.$position)){
        $slotsCritArray = array('published' => 1, 'is_global' => 1, 'block_position' => $position);
        if($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            if($this->get('security.context')->getToken()->getUser() && $this->get('security.context')->getToken()->getUser()->hasRole('ROLE_ADMIN')){
                $slotsCritArray = array('is_global' => 1, 'block_position' => $position);
            }
        }
        $slots = $this->getDoctrine()->getRepository('Gladtur\WebsiteBundle\Entity\WebpageSlot')->findBy($slotsCritArray, array('rank' => 'ASC'));
        $apcCache->save('global_'.$position, $slots, 5*86400);
        }
        else{
            $slots = $apcCache->fetch('global_'.$position);
        }
        return $this->render('WebsiteBundle:Default:slots_collection.html.twig',
            array('slots' => $slots, 'containerclasses'=>implode(' ', $containerclasses), 'slotclasses' => implode(' ', $slotclasses))
        );
    }

    /**
     * @Route("kategori/{category}", defaults = {"category":"all"}, name="locations_in_category")
     * @Template("WebsiteBundle:Default:index.html.twig")
     */
    public function categoryLocationsAction($category)
    {
        $locationsTopLimit = 100;
        $locations=new ArrayCollection();
        $request = $this->getRequest();
        if(($category !== 'all')){
            $categoryEnt = $this->getDoctrine()->getManager()->getRepository('GladturTagBundle:LocationCategory')->findOneBy(array('slug'=>$category));
            if($categoryEnt){
                if(!$categoryEnt->getIsTopcategory()){
                //$locations = $this->getDoctrine()->getManager()->getRepository('Gladtur\TagBundle\Entity\Location')->getResultsForCategoryAndPage($this->getDoctrine()->getManager(), $categoryEnt->getId(), 100);

                $subCLocations = $categoryEnt->getCategoriesLocations();
                foreach($subCLocations as $location){
                   if($locations->count() < $locationsTopLimit){
                     if($location->getPublished()){
                       $locations->add($location);
                     }
                   }
                }
                }
                else{
                    // Find the ones by top-category, the above finds the subcategorized ones! //
                    $locations = $this->getDoctrine()->getManager()->getRepository('Gladtur\TagBundle\Entity\Location')->getResultsForTopCategoryAndPage($this->getDoctrine()->getManager(), $categoryEnt->getId(), $locationsTopLimit);
                    //$locations = $this->getDoctrine()->getManager()->getRepository('GladturTagBundle:Location')->setMaxResults(100)->findBy(array('location_top_category'=>$categoryEnt, 'published'=>1), array('readableName'=> 'ASC'));
                }
            }
        }
        /*else{
            return $this->redirect('/');
        }*/
        $apcCache = new ApcCache();
        if(!$apcCache->contains('searchform_global')){
            $apcCache->save('searchform_global', $this->getSearchForm()->createView(), 86400);
        }
        else{
            $searchForm = $apcCache->fetch('searchform_global');
        }
        return array(
            'searchform'   => $searchForm,
            'locations' => $locations,
            'topcount' => null,
            'category'=>$category,
            'categoryEnt' => $categoryEnt,
            'pagetitle' => 'Se steder i kategorien ' . ucfirst(trim($categoryEnt->getReadableName())),
            'htmltitle'=>true,
        );
    }

    public function getSearchForm(){
        $defaultData = array('query'=>'Søgeord, Bynavn');
        $form = $this->createFormBuilder()->add('query', 'text', array('label'=>false, 'attr' => array('placeholder' => 'Søgeord, Bynavn')))->add('category', 'entity', array('required'=>false, 'class'=>'Gladtur\TagBundle\Entity\LocationCategory', 'multiple'=>false,'expanded'=>true, 'attr'=>array('class'=>'categories_list'), 'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('loccat')->where('loccat.isTopcategory=1')->orderBy('loccat.id', 'ASC');
                }))->getForm();
        return $form;
    }

    /**
     * @Template("WebsiteBundle:Locations:comment_block.html.twig")
     */
    public function latestCommentsAction($limit = null){
        $comments = $this->getDoctrine()->getManager()->createQuery("select c from Gladtur\TagBundle\Entity\UserLocationComments c where c.deletedAt is null order by c.created DESC")->setMaxResults(20)->getResult();
        return array(
            'comments' => $comments,
            'block_view' => true,
        );
    }

    /**
     * @Route("_ajax_setgeolocationprompted", name="setGeolocationPrompted")
     */
    public function setGeolocationPromptedAction(Request $request){
        $this->get('session')->set('userGeolocAnswer', $request->get('usergeolocanswer', 'rejected'));
        // Rådhuspladsen GetCoords: 55.675283,12.570163
        $this->get('session')->set('ulat', $request->get('ulat', 55.675283));
        $this->get('session')->set('ulng', $request->get('ulng', 12.570163));
        return new JsonResponse(array());
    }

    /**
     * @Route("_test_postget")
     */
    public function testPostGetAction(){
        $now = time();
        $secured = false;
        if($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            $secured = 'IS_AUTHENTICATED_FULLY';
        }
        $request = $this->getRequest();
        $userAgent = $request->headers->get('User-Agent');
        $clientIP = $request->getClientIp();
        // the URI being requested (e.g. /about) minus any query parameters
        $pathinfo = $request->getPathInfo();
        $method = $request->getMethod();
        $getParams = $request->query->all();
        foreach($getParams as $paramName => $paramValue){
            if($paramName == 'locationid'){
                $location = $this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\Location')->find($paramValue);
                $getParams['location_by_id'] = $location->getSlug() . ' (By ID: ' . $paramValue. ')';
            }
        }
        // $request->request->all() works for content type: application/x-www-form-urlencoded, so no result for application/json
        $postParams = $request->request->all();
        // $request->getContent() works for application/json, and NOT for application/x-www-form-urlencoded
        $jsonPostParams = json_decode($request->getContent(), true);
        if(isset($jsonPostParams['token'])) $secured = 'token';
        $totalrequest = array('unixtime'=>$now, 'date (d/m/Y)' => date("d/m/Y", $now),'time'=>date("H:i:s", $now), 'IP'=>$clientIP, 'User-Agent'=>$userAgent, 'controller'=>$request->attributes->get('_controller'),'path'=>$pathinfo, 'method' => $method, 'getParams' => $getParams, 'postParams' => $postParams, 'jsonPostParams' => $jsonPostParams, 'secured' => $secured);
        return new JsonResponse($totalrequest);
    }
}
