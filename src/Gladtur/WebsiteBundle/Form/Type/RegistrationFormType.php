<?php
/**
 * Created by PhpStorm.
 * User: mortenthorpe
 * Date: 11/11/13
 * Time: 11:37
 */

namespace Gladtur\WebsiteBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface as OptionsResolverInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Doctrine\ORM\EntityRepository;

/**
 * Class RegistrationFormType
 * See https://github.com/FriendsOfSymfony/FOSUserBundle/blob/master/Resources/doc/overriding_forms.md
 * Provides overridden Form for user-profile for FOSUserBundle, see also defined Services in /app/config/config.yml -> services section
 */
class RegistrationFormType extends BaseType {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('profile', null, array('label' => 'Vælg profil', 'required'=> true));
        $builder->add('freeprofileTags', 'entity', array('required'=>false,'class'=>'Gladtur\TagBundle\Entity\Tag', 'property'=>'readableName','multiple'=>true, 'expanded'=>true, 'label' => 'Anmeldelser', 'query_builder' =>
                function (EntityRepository $repository){
                    $qb = $repository->createQueryBuilder('tags')
                        ->innerJoin('tags.profiles', 'profile');
                    $qb->andWhere('tags.published = 1')->orderBy('tags.id', 'ASC');
                    return $qb;
                }));
        $builder->add('newsletter', null, array('label' => 'Vil du modtage nyhedsbreve fra GladTur?'))->add('contact', null, array('label' => 'Må GladTur kontakte dig fra tid til anden?'));
    }

    public function getName()
    {
        return 'gladtur_user_registration';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Gladtur\TagBundle\Entity\User',
                'intention'  => 'registration',
                'translation_domain', 'FOSUserBundle',
            ));
    }
}