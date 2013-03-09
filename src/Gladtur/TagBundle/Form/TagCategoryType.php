<?php

namespace Gladtur\TagBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile; // Upload with Doctrine Entity Backed data: http://symfony.com/doc/2.0/cookbook/doctrine/file_uploads.html

class TagCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$catChoices=range(1,10);
		$weightChoices=range(0,20);
        $builder
//            ->add('catid', 'choice', array('choices'=>$catChoices, 'label'=>'Kategori nummer'))
				->add('published','checkbox', array('label'=>'Skal vises?', 'required'=>false))
            ->add('isGeneral', 'checkbox', array('label'=>'Kategorien skal ALTID vises?','required'=>false))
				->add('readableName','text', array('label'=>'Kategori navn'))
					->add('textDescription', 'textarea', array('label'=>'Beskrivelse', 'required'=>false))
			->add('weight', 'choice', array('choices'=>$weightChoices, 'label'=>'Rangering'))
			//->add('iconFilepath', 'file', array('label'=>'Kategori ikon', 'required'=>false))
            ->add('image', 'file', array('label'=>'Kategori ikon', 'required'=>false))
				
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gladtur\TagBundle\Entity\TagCategory'
        ));
    }

    public function getName()
    {
        return 'gladtur_tagbundle_tagcategorytype';
    }
}
