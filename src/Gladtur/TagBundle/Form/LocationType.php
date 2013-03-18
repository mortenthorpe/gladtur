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
            ->add('readableName', 'text', array('label'=>'Navn som fremvist','required'=>false))
            ->add('latitude', 'text', array('label'=>'Breddegrad','required'=>false))
            ->add('longitude', 'text', array('label'=>'Længdegrad','required'=>false))
            ->add('published', 'checkbox', array('label'=>'Skal fremvises?','required'=>false))
            ->add('addressZip', 'text', array('label'=>'Postnr.','required'=>false))
            ->add('addressCountry', 'choice', array('label'=>'Land','required'=>false, 'choices'=>array('DK'=>'Danmark'), 'empty_value' => false))
            ->add('addressCity', 'text', array('label'=>'By','required'=>false))
            ->add('addressStreet', 'text', array('label'=>'Gade/vej navn','required'=>false))
            ->add('addressExtd', 'text', array('label'=>'Yderligere adresse','required'=>false))
            ->add('phone', 'text', array('label'=>'Telefon','required'=>false))
            ->add('mail', 'email', array('label'=>'Email','required'=>false))
            ->add('homepage', 'url', array('label'=>'Hjemmeside adresse','required'=>false))
            ->add('contactPerson', 'text', array('label'=>'Kontaktperson','required'=>false))
//            ->add('mediapath')
//            ->add('userLocationData','entity',array('empty_value'=>'-- Endnu ingen --','required'=>false, 'class'=>'GladturTagBundle:UserLocationData','label'=>'Stedets bruger bidragede data', 'multiple'=>'true','required'=>false))
// Grouped choices tutorial: https://groups.google.com/forum/#!msg/symfony2/2xuYxbOF38M/NOxs6wLJfr4J //
            ->add('locationCategory', 'entity', array('empty_value'=>'-- Endnu ingen --','required'=>false, 'class'=>'GladturTagBundle:LocationCategory','label'=>'Stedets Kategorier', 'multiple'=>'true', 'expanded'=>'true', 'property'=>'readableName', 'attr'=>array('class'=>'categories','required'=>false)))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gladtur\TagBundle\Entity\Location',
            'cascade_validation' => true));
    }


    public function getName()
    {
        return 'gladtur_tagbundle_locationtype';
    }
}
