<?php

namespace Gladtur\TagBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Gladtur\TagBundle\Form\TagType;
use Gladtur\TagBundle\Entity\Tag;

class TvguserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('readableName', 'text', array('label'=>'Brugerprofil-navn', 'attr'=>array('class'=>'profileName')))
        ->add('avatar', 'file', array('label'=>'App profil ikon (PNG, GIF)', 'image_path' => 'webPath', 'required'=>false, 'attr'=>array('class'=>'userprofile_image')))
        ->add('webavatar', 'file', array('label'=>'Website profil ikon (PNG, GIF)', 'image_path' => 'webPathSite', 'required'=>false, 'attr'=>array('class'=>'userprofile_image')));
        //$builder->add('tagCategories', 'entity', array('class'=>'GladturTagBundle:TagCategory', 'label'=>'Sted-egenskabs-kategorier/egenskaber for profilen', 'multiple'=>true, 'expanded'=>true));
        //$builder->add('tags', 'collection', array('type'=>new TagType()));
        $builder->add('tags', 'entity', array('label'=>'Profilens sted-egenskaber', 'class'=>'GladturTagBundle:Tag', 'multiple'=>true, 'expanded'=>true));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gladtur\TagBundle\Entity\TvguserProfile',
           // 'csrf_protection'   => false,
        ));
    }

    public function getName()
    {
        return 'tvguserprofiletype';
    }
}
