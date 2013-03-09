<?php

namespace Gladtur\TagBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('latitude')
            ->add('longitude')
            ->add('readableName')
            ->add('published')
            ->add('addressZip')
            ->add('addressCountry')
            ->add('addressCity')
            ->add('addressStreet')
            ->add('addressExtd')
            ->add('phone')
            ->add('mail')
            ->add('homepage')
            ->add('contactPerson')
//            ->add('mediapath')
          //  ->add('userLocationData','entity',array('empty_value'=>'-- Endnu ingen --','required'=>false, 'class'=>'GladturTagBundle:UserLocationData','label'=>'Stedets bruger bidragede data', 'multiple'=>'true'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gladtur\TagBundle\Entity\Location'
        ));
    }

    public function getName()
    {
        return 'gladtur_tagbundle_locationtype';
    }
}
