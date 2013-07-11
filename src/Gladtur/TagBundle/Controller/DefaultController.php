<?php

namespace Gladtur\TagBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
		$menuItems = array('tagIndex' => '@tag', 'location_tagIndex' => '@location_tag', 'locationIndex' =>'@location', 'tag_categoryIndex' => '@tagcategoryIndex' );
        return $this->render('GladturTagBundle:Default:index.html.twig', array('menuItems' => $menuItems));
    }
}
