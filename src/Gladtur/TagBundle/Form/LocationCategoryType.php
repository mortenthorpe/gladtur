<?php

namespace Gladtur\TagBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LocationCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$builder->add('locationCategory', 'entity', array('empty_value'=>'-- Endnu ingen --','required'=>false, 'class'=>'GladturTagBundle:LocationCategory','label'=>'Stedets Kategorier', 'multiple'=>'true', 'expanded'=>'true', 'property'=>'readableName', 'attr'=>array('class'=>'categories')));
       $builder->add('readableName');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Gladtur\TagBundle\Entity\LocationCategory',
               // 'cascade_validation' => true,
            ));
    }


    public function getName()
    {
        return 'locationCategory';
    }

}