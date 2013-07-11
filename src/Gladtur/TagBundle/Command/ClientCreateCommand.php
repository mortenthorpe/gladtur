<?php
 
namespace Gladtur\TagBundle\Command;
 
use FOS\OAuthServerBundle\Model\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
 
class ClientCreateCommand extends ContainerAwareCommand
{
    protected function configure ()
    {
        $this
            ->setName('mobile:client:create')
            ->setDescription('Creates a new OAuth V2 client')
            //->addOption('redirect-uri', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Sets the redirect uri. Use multiple times to set multiple uris.', null)
            //->addOption('grant-type', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Set allowed grant type. Use multiple times to set multiple grant types', null)
        ;
    }
 
    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $clientManager = $this->getContainer()->get('fos_oauth_server.client_manager.default');
        /** @var Client $client */
        $client = $clientManager->createClient();
       // $client->setRedirectUris($input->getOption('redirect-uri'));
        if($this->getContainer()->getParameter('livestate') || (($this->getContainer()->getParameter('livestate') == 'true'))){
            $client->setRedirectUris(array($this->getContainer()->getParameter('prod_url')));
        }
        else{
            $client->setRedirectUris(array($this->getContainer()->getParameter('dev_url')));
        }
        //$client->setAllowedGrantTypes($input->getOption('grant-type'));
        $client->setAllowedGrantTypes(array('password')); // Can also be 'password', but this returns no token //
        $clientManager->updateClient($client);
        $output->writeln(sprintf('Added a new client - password method - with public id <info>%s</info>.', $client->getPublicId()));
    }
}
