<?php

namespace Gladtur\TagBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LocationsSlugifyCommand extends ContainerAwareCommand
{
    protected function configure ()
    {
        $this
            ->setName('gladtur:locations:slugify')
            ->setDescription('Creates slugs for readable names for Locations in gladtur app')
        ;
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        /*
    }
        $locations= $this->getContainer()->get('doctrine')->getManager()->getRepository('GladturTagBundle:Location')->findBy(array('published'=>true));
        foreach($locations as $locationEntity){
            $slugtxt = $locationEntity->getReadableName().'-'.$locationEntity->getAddressStreetAndExtd();
            $locationEntity->slugify($slugtxt);
            $this->getContainer()->get('doctrine')->getManager()->persist($locationEntity);
        }
        $this->getContainer()->get('doctrine')->getManager()->flush();
        $this->getContainer()->get('doctrine')->getManager()->clear();
*/
    $q = $this->getContainer()->get('doctrine')->getManager()->createQuery("select l from Gladtur\TagBundle\Entity\Location l where l.published=1");
    $iterableResult = $q->iterate();
    // Create the original Slugs, which at this loop-point are allowed to have duplicates (n-licates), and NOT unique!
    while (($row = $iterableResult->next()) !== false) {
       // do stuff with the data in the row, $row[0] is always the object
       $locationEntity = $row[0];
       $slugtxt = trim($locationEntity->getNameClean().'-'.$locationEntity->getAddressStreetAndExtdClean());
        if(!$locationEntity->getAddressStreet() || $locationEntity->getAddressStreet() == ''){
            if($locationEntity->getOsmId()){
              $slugtxt = trim($locationEntity->getNameClean().'-'.$locationEntity->getOsmId());
            }
            else{
              continue;
            }
        }
        $locationEntity->slugify($slugtxt);
        $this->getContainer()->get('doctrine')->getManager()->persist($locationEntity);
        //$this->getContainer()->get('doctrine')->getManager()->detach($row[0]); // detach from Doctrine, so that it can be GC'd immediately
    }
        $this->getContainer()->get('doctrine')->getManager()->flush();
        $this->getContainer()->get('doctrine')->getManager()->clear();
        //$locationEntity = null;
        // Now ensure uniqueness for the slugs of the locations !
        // RAW SQL: SELECT l.slug, count(*) l FROM location l where l.published=1 GROUP BY l.slug HAVING l > 1;
        // SQL will select all slugs that are NOT unique!
        $q = $this->getContainer()->get('doctrine')->getManager()->createQuery("select l.slug, count(l.slug) lcount from Gladtur\TagBundle\Entity\Location l where l.published=1 group by l.slug having lcount > 1");
        $rs = $q->getResult(); // Associative array of fields, not objects!
        foreach($rs as $rs_row){
            $locationsWithSlug = $this->getContainer()->get('doctrine')->getManager()->getRepository('Gladtur\TagBundle\Entity\Location')->findBy(array('slug' => $rs_row['slug']));
            $currentCount = 1;
            foreach($locationsWithSlug as $location){
                if($currentCount == 1){
                    $location->slugify($rs_row['slug']);
                }
                else{
                    $location->slugify($rs_row['slug'].'-'.$currentCount);
                }
                $currentCount++;
                $this->getContainer()->get('doctrine')->getManager()->persist($location);
                $this->getContainer()->get('doctrine')->getManager()->flush();
                //$this->getContainer()->get('doctrine')->getManager()->clear();
            }
        }
    }
}