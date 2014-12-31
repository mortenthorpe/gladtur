<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 7/26/13
 * Time: 10:37 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Gladtur\TagBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SolariumIndexLocationsCommand extends ContainerAwareCommand{
    protected function configure ()
    {
        $this
            ->setName('solr:locations:index')
            ->setDescription('Indexes existing Gladtur Locations in SOLR Search');
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
       $locations = $this->getContainer()->get('doctrine')->getManager()->getRepository('GladturTagBundle:Location')->findAll();
        /**
         * @var \Gladtur\TagBundle\Entity\Location $locationEntity
         */
        $solrClient = $this->getContainer()->get('solarium.client');
        $update = $solrClient->createUpdate();
        $idxedLocationsCount = 0;
        foreach($locations as $locationEntity){
            if($locationEntity->getPublished() && $locationEntity->getSlug()){
                $update->addDocument($locationEntity->toSolariumDocument($solrClient));
                $idxedLocationsCount++;
            }
        }
        $update->addCommit();
        $rs = $solrClient->update($update);
        $output->writeln(sprintf('Indexed %d Locations in SOLR status: <info>%s</info>, Execution time (seconds): .<info>%s</info>', $idxedLocationsCount, $rs->getStatus(), $rs->getQueryTime()));
    }

}