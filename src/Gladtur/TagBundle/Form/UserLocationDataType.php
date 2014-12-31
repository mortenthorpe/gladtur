<?php

namespace Gladtur\TagBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class UserLocationDataType extends AbstractType{
//http://sf.khepin.com/2011/08/basic-usage-of-the-symfony2-collectiontype-form-field/
    public function buildForm(FormBuilderInterface $builder, array $options){
        $openingHoursEnabled = false;
        //$builder->add('media', 'collection', array('type'=>new UserLocationMediaType()));
      //  $builder->add('readableName', 'text', array('label'=>'Stedets navn','required'=>false))
        $builder->add('phone', 'text', array('label'=>'Telefon','required'=>false))
            ->add('mail', 'email', array('label'=>'Email','required'=>false))
            ->add('contactPerson', 'text', array('label'=>'Kontaktperson','required'=>false))
            ->add('txtDescription', 'textarea', array('label'=>'Kort beskrivelse'));
        if($openingHoursEnabled){
        $builder->add('daysHoursOpenClosed', 'collection', array(
                'type'   => 'gladtur_hours_open',
                'allow_add'    => true,
                'options'  => array(
                    'required'  => false,
                    'attr'      => array('class' => 'textfield openinghours'),
                    'data_class' => 'Gladtur\TagBundle\Entity\UserLocationHours',
                )));
        }
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