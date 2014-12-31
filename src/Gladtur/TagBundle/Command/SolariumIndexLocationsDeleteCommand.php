<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 7/26/13
 * Time: 11:53 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Gladtur\TagBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class SolariumIndexLocationsDeleteCommand extends ContainerAwareCommand{
    protected function configure ()
    {
        $this
            ->setName('solr:locations:clearindex')
            ->setDescription('Clears all indexes of Gladtur Locations in SOLR Search index');
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $solrClient = $this->getContainer()->get('solarium.client');
        $update = $solrClient->createUpdate();
        $update->addDeleteQuery('*:*');
        $update->addCommit();
        $rs = $solrClient->update($update);
        $output->writeln(sprintf('Cleared indexed Locations in SOLR status: <info>%s</info>, Execution time (seconds): .<info>%s</info>', $rs->getStatus(), $rs->getQueryTime()));
    }

}