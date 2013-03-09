<?php

namespace Gladtur\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        if($this->getUser() && $this->getUser()->hasRole('ROLE_ADMIN')){
            return $this->redirect('/admin/tag');
        }
        if($this->getUser() && $this->getUser()->hasRole('ROLE_TVGUSER')){
            return $this->redirect('/test');
        }
        return $this->redirect('/login');
		$menuItems = array('Oversigt over Tags' => 'tag', 'Oversigt over stedtags' => 'locationtag', 'Oversigt over steder' =>'location', 'Oversigt over tag-kategorier' => 'tagcategory');
        return $this->render('GladturTagBundle:Default:index.html.twig', array('menuItems' => $menuItems));
    }
}
