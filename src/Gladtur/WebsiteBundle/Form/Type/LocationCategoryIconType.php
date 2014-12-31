<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mortenthorpe
 * Date: 8/10/13
 * Time: 10:37 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Gladtur\WebsiteBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class LocationCategoryIconType extends AbstractType{

    public function getExtendedType()
    {
        return 'choice';
    }

    public $options;
    public function __construct($constructOptions = null){
        $this->options = ($constructOptions)?$constructOptions:array();
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'class'=>'GladturTagBundle:LocationCategory',
                'entity' => array('multiple'=>false, 'expanded'=>true, 'property'=>'readableName', 'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('loccat')->where('loccat.isTopcategory=1')->orderBy('loccat.readableName', 'ASC');
                }
                ),
            )
        );
    }

    public function getName()
    {
        return 'location_category_icon';
    }

}