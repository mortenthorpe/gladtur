<?php

namespace Gladtur\TagBundle\Form;
use Gladtur\TagBundle\Entity\UserLocationData;
use Symfony\Component\Form as FormEventListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Gladtur\TagBundle\Form\Type\LocationCategoryNType;
use Doctrine\ORM\EntityRepository;

class LocationType extends AbstractType
{
    public function __construct($topCategory){
        $this->topCategory = $topCategory;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //// http://stackoverflow.com/questions/6716776/symfony-2-how-to-pass-data-to-formbuilder
        // http://stackoverflow.com/questions/11107821/symfony2-form-events-drop-down-lists - Children and parents dropdowns
        // Src from: http://aulatic.16mb.com/wordpress/2011/08/symfony2-dynamic-forms-an-event-driven-approach/
        //
       /* $formFactory = $builder->getFormFactory();
        $builder->addEventListener(FormEventListener\FormEvents::PRE_SET_DATA, function (FormEventListener\FormEvent $event) use ($formFactory) {
            $form = $event->getForm();
            $data = (array) $event->getData()->getReadableName();
        $form
            ->add($formFactory->createNamed('readableName', 'text'))
            ->add($formFactory->createNamed('locationCategory', new LocationCategoryNType($data)));
        });*/
        $builder->add('readableName', 'text', array('label'=>'Stedets navn','required'=>false))
            ->add('latitude', 'hidden', array('label'=>'Breddegrad','required'=>false))
            ->add('longitude', 'hidden', array('label'=>'Længdegrad','required'=>false))
            ->add('published', 'checkbox', array('label'=>'Skal fremvises?','required'=>false))
            ->add('addressZip', 'text', array('label'=>'Postnr.','required'=>false))
            ->add('addressCountry', 'choice', array('label'=>'Land','required'=>false, 'choices'=>array('DK'=>'Danmark'), 'empty_value' => false))
            ->add('addressCity', 'text', array('label'=>'By','required'=>false))
            ->add('addressStreet', 'text', array('label'=>'Gade/vej navn','required'=>false))
            //->add('addressExtd', 'text', array('label'=>'Yderligere adresse','required'=>false))
            ->add('homepage', 'url', array('label'=>'Hjemmeside adresse','required'=>false));
            /*->add('phone', 'text', array('label'=>'Telefon','required'=>false))
            ->add('mail', 'email', array('label'=>'Email','required'=>false))

            ->add('contactPerson', 'text', array('label'=>'Kontaktperson','required'=>false))*/
            /*->add('locationCategory', 'entity', array('attr'=>array('class'=>json_encode($data)), 'class'=>'GladturTagBundle:LocationCategory','label'=>'Stedets Kategorier', 'multiple'=>'true', 'expanded'=>'true', 'property'=>'readableName', 'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('loccat')->orderBy('loccat.id', 'ASC');
                },));*/
            //->add('locationCategory', 'collection', array('type' => new LocationCategoryType()));
            //$builder->add('userLocationData', new UserLocationDataType(), array())

        $builder->add('location_top_category', new LocationCategoryNType(), array('label'=>'Stedets top kategori', 'empty_value'=>'-- Vælg top-kategori --','multiple'=>false, 'expanded'=>false, 'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('loccat')->where('loccat.isTopcategory=1')->orderBy('loccat.id', 'ASC');}));
        $topCategory = $this->topCategory;
        $builder->add('locationCategories', new LocationCategoryNType(), array('label'=>'Stedets øvrige kategorier', 'multiple'=>true, 'expanded'=>true, 'query_builder' => function(EntityRepository $er) use ($topCategory) {
                return $er->createQueryBuilder('loccat')->where('loccat.parentCategory='.$topCategory->getId())->orderBy('loccat.id', 'ASC');}));
        //$builder->add('userLocationTagData');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gladtur\TagBundle\Entity\Location',
            'cascade_validation' => true));
    }


    public function getName()
    {
        return 'gladtur_tagbundle_locationtype';
    }
}
