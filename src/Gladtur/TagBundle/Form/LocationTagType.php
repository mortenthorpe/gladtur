<?php

namespace Gladtur\TagBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LocationTagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('locationTagProperties')
            ->add('tag', 'text', array('label'=>'Sted egenskab'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gladtur\TagBundle\Entity\LocationTag'
        ));
    }

    public function getName()
    {
        return 'gladtur_tagbundle_locationtagtype';
    }
	
	public function __toString(){
		return $this->getName();
	}
}
