<?php

namespace Gladtur\TagBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Gladtur\TagBundle\Form\Type\TagAndValueType;

class UserLocationTagDataType extends AbstractType{
//http://sf.khepin.com/2011/08/basic-usage-of-the-symfony2-collectiontype-form-field/
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add('tagsandvalues', 'collection', array('type' => new TagAndValueType()));
        $builder->add('tag')
            ->add('tagvalue', 'choice', array('choices' => array(0,1,2,3), 'multiple'=>false, 'expanded'=>false));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Gladtur\TagBundle\Entity\UserLocationTagData',
                'cascade_validation' => true));
    }


    public function getName()
    {
        return 'gladtur_tagbundle_user_location_tagdata';
    }
}