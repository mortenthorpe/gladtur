<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new Gladtur\AdminBundle\GladturAdminBundle(),
            new Gladtur\TagBundle\GladturTagBundle(),
            new Gladtur\TestoneBundle\GladturTestoneBundle(),
//            new Gladtur\UserBundle\GladturUserBundle(),
			new FOS\UserBundle\FOSUserBundle(),
	//		new Gladtur\UserBundle\Entity\User(),
            //new FOS\OAuthServerBundle\FOSOAuthServerBundle(),
	    //new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
            new Google\GeolocationBundle\GoogleGeolocationBundle(),
	    new JMS\SerializerBundle\JMSSerializerBundle(),
	new Bmatzner\FoundationBundle\BmatznerFoundationBundle(),
	new Bmatzner\JQueryBundle\BmatznerJQueryBundle(),
            new Bmatzner\JQueryUIBundle\BmatznerJQueryUIBundle(),
            new Ivory\GoogleMapBundle\IvoryGoogleMapBundle(),
 //new Neutron\FormBundle\NeutronFormBundle(),
        // if you use form types which require neutron/datagrid-bundle
 //       new Neutron\DataGridBundle\NeutronDataGridBundle(),
        // if you use plupload
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
        //new Avalanche\Bundle\ImagineBundle\AvalancheImagineBundle(), // Super-seeded by LIIPBundle
            new Gladtur\MobileBundle\GladturMobileBundle(),
          //  new FOS\RestBundle\FOSRestBundle(),
            new AntiMattr\GoogleBundle\GoogleBundle(),
            new Nelmio\SolariumBundle\NelmioSolariumBundle(),
            new JMS\TranslationBundle\JMSTranslationBundle(),
            //new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Gladtur\WebsiteBundle\WebsiteBundle(),
            new Liip\ImagineBundle\LiipImagineBundle(),
         //   new CCDNUser\SecurityBundle\CCDNUserSecurityBundle(),
            new Oneup\UploaderBundle\OneupUploaderBundle(),
            new Divi\AjaxLoginBundle\DiviAjaxLoginBundle(),
            //new Doctrine\Bundle\MongoDBBundle\DoctrineMongoDBBundle(),
            //new Blackshawk\SymfonyReactorBundle\BlackshawkSymfonyReactorBundle(),
            //new UAParser\UAParserSf2(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');

    }
}
