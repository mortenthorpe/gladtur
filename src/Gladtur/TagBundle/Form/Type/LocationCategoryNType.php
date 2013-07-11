<?php
namespace Gladtur\TagBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class LocationCategoryNType extends AbstractType{

    public $options;
    public function __construct($constructOptions = null){
        $this->options = ($constructOptions)?$constructOptions:array();
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class'=>'GladturTagBundle:LocationCategory',
            'entity' => array('multiple'=>'true', 'property'=>'readableName', 'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('loccat')->orderBy('loccat.id', 'ASC');
                }
            ),
            )
        );
    }

    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'location_category';
    }

}