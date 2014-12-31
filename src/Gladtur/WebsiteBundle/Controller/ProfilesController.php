<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 8/12/13
 * Time: 2:10 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Gladtur\WebsiteBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Gladtur\TagBundle\Entity\TvguserProfile;
use Gladtur\TagBundle\Entity\UserLocationData;
use Gladtur\TagBundle\Form\TvguserProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


class ProfilesController extends Controller{

    /**
     * @Route("_selectprofile_and_location", name="_selectprofile_and_location")
     * @Template("WebsiteBundle:Default:_profile_selector.html.twig")
     */
    public function profileSelectorAction($selectedProfileId=3){
        if($this->getUser()){
            $profilesCrit = array();
        }
        else{
            $profilesCrit = array('individualized' => false);
        }
        $profiles = $this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\TvguserProfile')->findBy($profilesCrit, array('rank'=>'ASC'));
        $defaultData = array('profile'=>$selectedProfileId);
        $selectorForm = $this->createFormBuilder($defaultData);
        $this->addProfileSelectorField($selectorForm);
        $selectedProfile = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\TvguserProfile', $selectedProfileId);
        $selectedProfileImg = ($selectedProfile->getPath())?$selectedProfile->getPath():'12_Inddividuelt-tilpasset-brugerprofil.png';
        return array(
            'profiles' => $profiles,
            'selectedprofile' => $selectedProfileId,
            'selectedprofile_image' => $selectedProfileImg,
            'profile_selector' => $selectorForm->getForm()->createView(),
        );
    }

    private function addProfileSelectorField(FormBuilder &$baseForm){
        $baseForm->add('profile', 'entity', array('label'=>'Vælg din profil','class'=>'GladturTagBundle:TvguserProfile', 'query_builder' =>
                function (EntityRepository $repository){
                    $qb = $repository->createQueryBuilder('profiles')->where('profiles.individualized != 1')->orderBy('profiles.id', 'ASC');
                    return $qb;
                }));
    }

    private function addLatLngHiddenFields(FormBuilder &$baseForm){
        // Rådhuspladsen GetCoords: 55.675283,12.570163
        $baseForm->add('lat', 'hidden', array('data' => 55.675283));
        $baseForm->add('lng', 'hidden', array('data' => 12.570163));
    }
    /**
     * @Route("_profiles_accessibilities_icons", name = "_profiles_accessibilities_icons")
     * @Template("")
     */
    public function profileIconsAction(){
        $profiles = $this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\TvguserProfile')->findAll();
        $htmlout = '<form id="profile_icons">';
        foreach($profiles as $profile){
          $iconPath = ($profile->getPath())?$profile->getPath():'12_Inddividuelt-tilpasset-brugerprofil.png';
          $htmlout .= '<input type="hidden" id="profile_icon_' . $profile->getId() . '" value = "' . $iconPath . '" />';
        }
        $htmlout .= '</form>';
        return new Response($htmlout);
    }

    /**
     * @Route("_profile_icon", name = "_profile_icon")
     * @Template("")
     */
    public function getIconForProfileIdAction(Request $request){
        $profile = $this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\TvguserProfile')->find($request->get('profileid', 3));
        return new JsonResponse(array('icon' => $profile->getPath()));
    }

    /**
     * @Route("_profiles_accessibilities", name = "_profiles_accessibilities")
     */
    public function profileSelectedAjaxAction(Request $request){
        $profileId = $request->get('profile');
        $profile = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\TvguserProfile', $profileId);
        $locationId = $request->get('location');
        $location = $this->getDoctrine()->getManager()->find('Gladtur\TagBundle\Entity\Location', $locationId);
        $userLocTagData = $this->getDoctrine()->getManager()->getRepository('Gladtur\TagBundle\Entity\UserLocationTagData')->findBy(array('location'=>$location, 'user_profile'=>$profile), array('updated'=>'ASC'));
        $userLocTags = array();
        foreach($userLocTagData as $tagdata){
            $userLocTags[$tagdata->getTag()->getId()] = $tagdata;
        }
        $profileIcon = ($profile->getPath())?$profile->getPath():'12_Inddividuelt-tilpasset-brugerprofil.png';
        //return new Response(json_encode($userLocTagDataNames));
        $outhtml='';
        foreach($userLocTags as $userLocTagDataElem){
            $outhtml.=$this->render('WebsiteBundle:Locations:_accessibility_listitem.html.twig', array('user_location_data'=>$userLocTagDataElem))->getContent();
        }
        return new JsonResponse(array('listing'=>$outhtml, 'icon'=>$profileIcon));
    }


    /**
     * @Route("profiles/properties", name="_ajax_profileproperties")
     */
    public function getPropertiesForProfile(Request $request){
        $htmlOptionsOut = '';
        $profileid = $request->get('profileid', $this->getUser()->getProfile()->getId());
        $em = $this->getDoctrine()->getManager();
        $tagEntities = array();
        if($profileid){
            $profile = $em->find('Gladtur\TagBundle\Entity\TvguserProfile', $profileid);
            if($profile && !$profile->getIndividualized()){
                $tagEntities = array();
            }
            // Dealing with a profile where relations to properties/tags are loosely defined.
            if($profile && $profile->getIndividualized()){
                $tagEntities = $em->getRepository('GladturTagBundle:Tag')->findBy(array('published'=>1), array('id' => 'ASC'));
                $userProfileByTags = $this->getUser()->getFreeProfile();
                // The free profile is the active one for the user at the time of this request
                /*if($userProfileByTags && $userProfileByTags->getProfileActive()){
                    $tagEntities = $em->getRepository('GladturTagBundle:Tag')->findAll();
                    //$tagEntities = $userProfileByTags->getProfileTags();
                }*/
                $htmlOptionsOut.='<input type="hidden" id="fos_user_profile_form_isindividualized" name="fos_user_profile_form[isindividualized]" value="1"/>';
            }
            else{
                return new Response('');
            }
        }
        else{
            $tagEntities = array();
        }

        foreach($tagEntities as $tag){
            if($tag->getPublished()){
                $htmlOptionsOut .= '<div class="row"><input type="checkbox" id="fos_user_profile_form_freeprofileTags_' . $tag->getId() . '" name="fos_user_profile_form[freeprofileTags][]" value="' . $tag->getId() . '"/><label for="fos_user_profile_form_freeprofileTags_' . $tag->getId() . '">' . $tag->getReadableName() . '</label></div>';
            }
        }
        return new Response($htmlOptionsOut);
    }

    public function getIndividualizedAction(){
        $htmlOut = '';
        foreach($this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\TvguserProfile')->getIndividualizedProfileNamesArr() as $individualizedProfile){
            $htmlOut .= $individualizedProfile['readableName'];
        }
        return new Response($htmlOut);
    }

    public function getIndividualizedIdAction(){
        $idOutput = null;
        foreach($this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\TvguserProfile')->getIndividualizedProfileIdsArr() as $individualizedProfile){
           $idOutput = intval($individualizedProfile['id']);
        }
        return new Response($idOutput);
    }

    /**
     * @return Response
     * @Route("_anon_profile_lat_lng", name="_anon_profile_lat_lng")
     */
    public function anonProfileAndLatLngAction(){
        $referer = $this->getRequest()->server->get('HTTP_REFERER');
        $selectorForm = $this->createFormBuilder();
        $this->addProfileSelectorField($selectorForm);
        $selectedProfile = $this->getDoctrine()->getRepository('Gladtur\TagBundle\Entity\TvguserProfile')->findOneBy(array());
        $selectedProfileImg = ($selectedProfile->getPath())?$selectedProfile->getPath():'12_Inddividuelt-tilpasset-brugerprofil.png';
        $this->addLatLngHiddenFields($selectorForm);
        $map = $this->get('ivory_google_map.map');
        // Rådhuspladsen GetCoords: 55.675283,12.570163
        $map->setCenter(55.675283, 12.570163);
        $map->setLanguage('da');
        $map->setMapOption('zoom',10);
        $map->setStylesheetOption('width', '380px');
        $map->setHtmlContainerId('map_canvas_profileselect');
        return $this->render(
            'WebsiteBundle:Profiles:_anon_profile_and_latlng.html.twig',
            array(
                'form' => $selectorForm->getForm()->createView(),
                'map' => $map,
                'referer' => $referer,
                'selected_profile' => $selectedProfile,
                'selected_profile_img' => $selectedProfileImg,
            )
        );
    }

    /**
     * @Route("_set_profile_lat_lng", name="_set_profile_lat_lng")
     * @Method("POST")
     */
    public function setProfileAndLatLngAction(Request $request){
        $user = null;
        $this->get('session')->set('positionKnown', false);
        if($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            $user = $this->get('security.context')->getToken()->getUser();
        }
        $referer = $request->server->get('HTTP_REFERER');
        $req_form = $request->get('form', null);
        if($req_form){
            if(isset($req_form['profile']) && isset($req_form['lat']) && isset($req_form['lng'])){
               $this->get('session')->set('uprofile', $req_form['profile']);
               $this->get('session')->set('ulat', $req_form['lat']);
               $this->get('session')->set('ulng', $req_form['lng']);
               $this->get('session')->set('positionKnown', true);
               if($user){
                   $user->setLatitude($req_form['lat']);
                   $user->setLongitude($req_form['lng']);
                   $this->get('session')->set('uprofile', $user->getProfile()->getId());
                   $this->getDoctrine()->getManager()->persist($user);
                   $this->getDoctrine()->getManager()->flush();
               }
            }
        }
        return $this->redirect($referer);
    }
}