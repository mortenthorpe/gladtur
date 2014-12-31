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

class UserLocationDataType2 extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('daysHoursOpenClosed', 'collection', array(
            'type'   => 'text',
            'options'  => array(
                'required'  => false,
                'attr'      => array('class' => 'textfield openinghours')
            )));
        //$builder->add('daysHoursOpenClosed', 'text');
    }

    public function getName(){
        return 'user_location_data';
    }
} 