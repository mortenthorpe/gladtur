<?php
/**
 * Created by PhpStorm.
 * User: mortenthorpe
 * Date: 17/12/13
 * Time: 10:00
 */

namespace Gladtur\WebsiteBundle\Form\Type;

use Gladtur\TagBundle\Entity\UserLocationData;
use Gladtur\TagBundle\Form\UserLocationDataType;
use Symfony\Component\Form as FormEventListener;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Gladtur\TagBundle\Form\Type\LocationCategoryNType;
use Doctrine\ORM\EntityRepository;

class OpeninghoursType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*$builder->add('openinghours', 'collection', array(
            'type'   => 'text',
            // these options are passed to each "email" type
            'options'  => array(
                'required'  => false,
                'attr'      => array('class' => 'textfield openinghours')
            )));*/
        $builder->add('openinghour', 'text');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Gladtur\TagBundle\Entity\UserLocationHours',
            ));
    }

    public function getName(){
        return 'location_opening_hours';
    }
} 