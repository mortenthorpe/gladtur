<?php

namespace Gladtur\TagBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TvguserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('readableName', 'text', array('label'=>'Brugerprofil-navn', 'attr'=>array('class'=>'profileName')))
            ->add('avatar', 'file', array('image_path' => 'webPath', 'required'=>'false', 'attr'=>array('class'=>'userprofile_image')));

        $builder->add('tagCategories', 'entity', array('class'=>'GladturTagBundle:TagCategory', 'label'=>'Sted-egenskabs-kategorier tilknyttet profilen', 'multiple'=>true, 'expanded'=>true));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gladtur\TagBundle\Entity\TvguserProfile'
        ));
    }

    public function getName()
    {
        return 'gladtur_tagbundle_tvguserprofiletype';
    }
}
