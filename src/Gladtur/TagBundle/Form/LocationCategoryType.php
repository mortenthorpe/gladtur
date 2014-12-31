<?php

namespace Gladtur\TagBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LocationCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('isTopcategory', 'checkbox', array('label'=>'Denne kategori er en top-kategori!', 'required'=>false));
        $builder->add('parentCategory', 'entity', array('empty_value'=>'-- Ingen --','required'=>false, 'class'=>'GladturTagBundle:LocationCategory','label'=>'Tilhører top-kategorien', 'multiple'=>false, 'expanded'=>false, 'property'=>'readableName', 'attr'=>array('class'=>'categories'), 'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('loccat')->where('loccat.isTopcategory=true')->orderBy('loccat.readableName', 'ASC');
            }));
       $builder->add('readableName', 'text', array('label'=>'Kategoriens navn'));
       $builder->add('iconVirtual', 'file', array('label'=>'Kategoriens ikon', 'image_path' => 'webPath', 'required'=>false));
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