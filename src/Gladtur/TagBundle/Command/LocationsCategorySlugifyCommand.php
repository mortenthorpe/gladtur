<?php

namespace Gladtur\TagBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LocationsCategorySlugifyCommand extends ContainerAwareCommand
{
    protected function configure ()
    {
        $this
            ->setName('gladtur:locations:category:slugify')
            ->setDescription('Creates slugs for readable names for Location Categories in gladtur app')
        ;
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $locationCategories = $this->getContainer()->get('doctrine')->getManager()->getRepository('GladturTagBundle:LocationCategory')->findAll();
        foreach($locationCategories as $locationCategoryEntity){
          $locationCategoryEntity->slugify($locationCategoryEntity->getReadableName());
          $this->getContainer()->get('doctrine')->getManager()->persist($locationCategoryEntity);
        }
        $this->getContainer()->get('doctrine')->getManager()->flush();
    }
}
