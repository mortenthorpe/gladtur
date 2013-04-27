<?php

namespace Gladtur\TagBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserLocationDataType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add('readableName', 'text', array('label'=>'Stedets navn','required'=>false))
            ->add('latitude', 'hidden', array('label'=>'Breddegrad','required'=>false))
            ->add('longitude', 'hidden', array('label'=>'Længdegrad','required'=>false))
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
        ->add('hoursOpeningtime', 'time')
        ->add('hoursClosingtime', 'time')
        ->add('txtDescription', 'text')
        ->add('txtComment', 'text')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Gladtur\TagBundle\Entity\UserLocationData',
                'cascade_validation' => true));
    }


    public function getName()
    {
        return 'gladtur_tagbundle_user_location_datatype';
    }
}