<?php

namespace Gladtur\TagBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserLocationMediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('filepath', 'file', array('label' => 'Billede/film-upload'));
        //$builder->add('mimetype', 'text');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Gladtur\TagBundle\Entity\UserLocationMedia',
                // 'cascade_validation' => true,
            )
        );
    }


    public function getName()
    {
        return 'gladtur_media_type';
    }

}