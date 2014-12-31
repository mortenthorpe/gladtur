<?php

namespace Gladtur\TagBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('readableName', 'text', array('label'=>'Egenskabens navn'))
            ->add('iconVirtual', 'file', array('label'=>'Egenskabens ikon', 'image_path' => 'webPath', 'required'=>false))
				/*->add('tagCategory','entity',array('empty_value'=>'-- Uden Kategori --','required'=>false, 'class'=>'GladturTagBundle:TagCategory','label'=>'Sted egenskabs kategori'))
	        ->add('published', 'checkbox', array('label'=>'Skal vises?','required'=>false,))
	         ->add('textDescription', 'textarea',array('label'=>'Tekst beskrivelse','required'=>false,))
			//->add('relevance', 'choice', array('expanded'=>true, 'multiple'=>true, 'choices'=>array(0=>'Kørestol',1=>'Gangbesvær', 2=>'Syn', 4=>'Hørelse', 5=>'Allergi'), 'label'=>'Relevant for tilgængelighed'))
//            ->add('tvguserProfile', 'entity', array('expanded'=>true, 'multiple'=>true))*/
            ->add('textDescription', 'textarea',array('label'=>'Tekst beskrivelse','required'=>false))
            ->add('location_categories', 'entity', array('multiple'=>true, 'expanded'=>true, 'required'=>false, 'label'=>'Skal gælde for kategorierne', 'class' => 'Gladtur\TagBundle\Entity\LocationCategory', 'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('loccat')->where('loccat.isTopcategory=1')->orderBy('loccat.id', 'ASC');}))
           // ->add('profiles', 'entity', array('class'=>'GladturTagBundle:TvguserProfile', 'label'=>'Relevant for bruger-profilerne','expanded'=>true, 'multiple'=>true))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gladtur\TagBundle\Entity\Tag'
        ));
    }

    public function getName()
    {
        return 'tag';
    }
	
	public function __toString(){
		return $this->getName();
	}
}
