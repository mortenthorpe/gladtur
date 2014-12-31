<?php
namespace Gladtur\TagBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class TagAndValueType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options){
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
        return 'gladtur_tagbundle_user_location_tag_value';
    }

}