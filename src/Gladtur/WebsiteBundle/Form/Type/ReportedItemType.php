<?php
/**
 * Created by PhpStorm.
 * User: mortenthorpe
 * Date: 29/01/14
 * Time: 13.20
 */

namespace Gladtur\WebsiteBundle\Form\Type;

use Symfony\Component\Form as FormEventListener;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;


class ReportedItemType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('user_report_txt', 'textarea', array('label' => 'Din indberetning'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Gladtur\TagBundle\Entity\ReportedItem',
                'csrf_protection' => false,
            ));
    }

    public function getName(){
        return 'reported_item';
    }
}