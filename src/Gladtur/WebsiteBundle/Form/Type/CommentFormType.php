<?php
/**
 * Created by PhpStorm.
 * User: mortenthorpe
 * Date: 06/01/14
 * Time: 14:11
 */

namespace Gladtur\WebsiteBundle\Form\Type;

use Symfony\Component\Form as FormEventListener;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class CommentFormType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('comment_txt', 'textarea', array('required'=>true, 'label' => 'Kommentar'));
        $builder->add('comment_image', 'file', array('cascade_validation' => true, 'required'=>false,'label' => 'Evt. billede'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Gladtur\TagBundle\Entity\UserLocationComments',
                'cascade_validation' => true,
                'csrf_protection' => false,
            ));
    }

    public function getName(){
        return 'location_comment';
    }
} 