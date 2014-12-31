<?php
/**
 * Created by PhpStorm.
 * User: mortenthorpe
 * Date: 04/12/13
 * Time: 14:30
 */

namespace Gladtur\WebsiteBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Gladtur\TagBundle\Entity\TvguserProfile;
use Gladtur\TagBundle\Entity\User;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Form;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Gladtur\TagBundle\Form\TagType;
use Gladtur\WebsiteBundle\Form\Type\ProfileTagType;

class ProfileTagType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $factory = $builder->getFormFactory();
        // Add profile-dependent tags/properties here.
        $builder->add('profile', 'entity', array('empty_value'=>false, 'class'=>'Gladtur\TagBundle\Entity\TvguserProfile'));
        $refreshTags = function($form, $profile) use ($factory){
                $form->add($factory->createNamed('tags', 'entity', null, array('class'=>'Gladtur\TagBundle\Entity\Tag', 'property'=>'readableName', 'label' => 'Tilgængeligheder 2', 'query_builder' =>
                            function (EntityRepository $repository) use ($profile){
                                $qb = $repository->createQueryBuilder('tags')
                                    ->innerJoin('tags.profiles', 'profile');

                                if($profile instanceof TvguserProfile) {
                                    $qb = $qb->where('tags.profiles = :profile')
                                        ->setParameter('profile', $profile);
                                } elseif(is_numeric($profile)) {
                                    $qb = $qb->where('profile.id = :profiles_id')
                                        ->setParameter('profiles_id', $profile);
                                } else {
                                    $qb = $qb->where('profile.id = 1');
                                }
                                return $qb;
                            })));
            };

            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($refreshTags) {
                    $form = $event->getForm();
                    $data = $event->getData();

                    if($data == null)
                        $refreshTags($form, null); //As of beta2, when a form is created setData(null) is called first

                    if($data instanceof User) {
                        $refreshTags($form, $data->getProfile());
                    }
                });
    }

    public function getName()
    {
        return 'profile_tags';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver){
        $resolver->setDefaults(array('data_class' => 'Gladtur\TagBundle\Entity\TvguserProfile'));
    }
} 