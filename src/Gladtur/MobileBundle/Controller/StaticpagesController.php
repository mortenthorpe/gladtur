<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 6/12/13
 * Time: 9:04 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Gladtur\MobileBundle\Controller;

use Gladtur\TagBundle\Controller\JsonController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\CssSelector\CssSelector;
use Symfony\Component\DomCrawler\Crawler;

class StaticpagesController extends JsonController
{
    /**
     * @Route("read/{pagename}", name="static_page")
     */
    public function indexAction($pagename)
    {
        $pageHTML = file_get_contents('staticpages/' . $pagename . '.html');
        $crawler = new Crawler($pageHTML);
        $ptitle = $crawler->filter('h1')->extract(array('_text'));
        $ptitle = $ptitle[0];

        return $this->render(
            'GladturMobileBundle:Default:staticpage.html.twig',
            array('pagetitle' => $ptitle, 'content' => $pageHTML, 'pagename' => $pagename)
        );

        // return parent::getJsonForData(array($request->getUriForPath('/read/terms'))); -- Returns FULL URL!
    }
}