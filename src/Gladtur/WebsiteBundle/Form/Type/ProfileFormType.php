<?php
/**
 * Created by PhpStorm.
 * User: mortenthorpe
 * Date: 11/11/13
 * Time: 11:37
 */

namespace Gladtur\WebsiteBundle\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
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

/**
 * Class ProfileFormType
 * See https://github.com/FriendsOfSymfony/FOSUserBundle/blob/master/Resources/doc/overriding_forms.md
 * Provides overridden Form for user-profile for FOSUserBundle, see also defined Services in /app/config/config.yml -> services section
 */
// Event-driven approach to making dependent dropdowns - For individualized profile!
// http://aulatic.16mb.com/wordpress/2011/08/symfony2-dynamic-forms-an-event-driven-approach/
class ProfileFormType extends BaseType {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $factory = $builder->getFormFactory();
        $builder->add('profile', 'entity', array('label'=>'Din profil', 'empty_value'=>false, 'class'=>'Gladtur\TagBundle\Entity\TvguserProfile', 'required'=>true));
        $builder->add('freeprofileTags', 'entity', array('required'=>false,'class'=>'Gladtur\TagBundle\Entity\Tag', 'property'=>'readableName','multiple'=>true, 'expanded'=>true, 'label' => 'Anmeldelser', 'query_builder' =>
                function (EntityRepository $repository){
                    $qb = $repository->createQueryBuilder('tags')
                        ->innerJoin('tags.profiles', 'profile');
                    $qb->andWhere('tags.published = 1')->orderBy('tags.id', 'ASC');
                    return $qb;
                }));
        // Add profile-dependent tags/properties here.
      /*  $refreshTags = function($form, $profile) use ($factory){
            if(!$profile || !$profile->getIndividualized()) return;
            $form->add($factory->createNamed('freeprofileTags', 'entity', null, array('required'=>false,'class'=>'Gladtur\TagBundle\Entity\Tag', 'property'=>'readableName','multiple'=>true, 'expanded'=>true, 'label' => 'Tilgængeligheder', 'query_builder' =>
                        function (EntityRepository $repository) use ($profile){
                            $qb = $repository->createQueryBuilder('tags')
                                ->innerJoin('tags.profiles', 'profile');
                            $qb->andWhere('tags.published = 1');
                            return $qb;
                        })));
        };
*/
        /*$builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event){
                $form = $event->getForm();
                $data = $event->getData();

                if($data == null)
                    return;

                if($data instanceof User) {
                    if($data->getProfile()->getIndividualized()){
                        $form->add('freeprofileTags', 'entity', array('required'=>false,'class'=>'Gladtur\TagBundle\Entity\Tag', 'property'=>'readableName','multiple'=>true, 'expanded'=>true, 'label' => 'Tilgængeligheder', 'query_builder' =>
                                function (EntityRepository $repository){
                                    $qb = $repository->createQueryBuilder('tags')
                                        ->innerJoin('tags.profiles', 'profile');
                                    $qb->andWhere('tags.published = 1')->orderBy('tags.id', 'ASC');
                                    return $qb;
                                }));
                        $form->add('isindividualized', 'hidden', array('property_path'=>'individualized','required'=>false));
                       // $refreshTags($form, $data->getProfile());
                    }
                }
            });*/

        $builder->add('newsletter', 'checkbox', array('label'=>'Jeg vil gerne modtage nyhedsbrevet', 'required' => false))->add('contact', 'checkbox', array('label'=>'GladFonden må gerne kontakte mig', 'required' => false))->add('deleted', 'checkbox', array('required' => false, 'label' => 'Afmeld min bruger - ved deaktivering'));

    }

    public function getName()
    {
        return 'gladtur_user_profile';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver){
        $resolver->setDefaults(array('data_class' => 'Gladtur\TagBundle\Entity\User', 'cascade_validation' => true));
    }


}