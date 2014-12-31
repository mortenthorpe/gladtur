<?php
/**
 * Created by PhpStorm.
 * User: mortenthorpe
 * Date: 17/12/13
 * Time: 13:44
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

class daysHoursOpenClosedType extends AbstractType{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('timesTxt', 'text', array('label' => false, 'attr' => array('class' => 'timestxt')));
        $builder->add('isclosed', 'checkbox', array('label' => 'Er lukket?', 'required' => false, 'attr' => array('class' => 'isclosed')));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Gladtur\TagBundle\Entity\UserLocationHours',
            ));
    }

    public function getName(){
        return 'gladtur_hours_open';
    }
} 