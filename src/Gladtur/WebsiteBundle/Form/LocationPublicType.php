<?php

namespace Gladtur\WebsiteBundle\Form;
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


class LocationPublicType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // http://stackoverflow.com/questions/11107821/symfony2-form-events-drop-down-lists - Children and parents dropdowns
        // Src from: http://aulatic.16mb.com/wordpress/2011/08/symfony2-dynamic-forms-an-event-driven-approach/
        $builder->add('admin_validated_boolean','checkbox', array('label'=>'Godkendt af administrator?', 'required' => false));
        $builder->add('readableName', 'text', array('label'=>'Stedets navn', 'required'=>true));
        $builder->add('location_top_category', new LocationCategoryNType(), array('cascade_validation' => true, 'label'=>'Stedets top kategori', 'empty_value'=>'-- Vælg top-kategori --','multiple'=>false, 'expanded'=>false,'required'=>true, 'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('loccat')->where('loccat.isTopcategory=1')->orderBy('loccat.id', 'ASC');}));
        $builder->add('homepage', 'url', array('label'=>'Hjemmeside adresse','required'=>false, 'default_protocol' => 'http'));
        $builder->add('userLocationDataLatest', new UserLocationDataType(), array('data_class'=> 'Gladtur\TagBundle\Entity\UserLocationData', 'label'=>null));

       /*
$builder->add('location_top_category', new LocationCategoryNType(), array('required'=>true, 'label'=>'Stedets top kategori', 'empty_value'=>'-- Vælg top-kategori --','multiple'=>false, 'expanded'=>false,'required'=>true, 'query_builder' => function(EntityRepository $er) {
            return $er->createQueryBuilder('loccat')->where('loccat.isTopcategory=1')->orderBy('loccat.id', 'ASC');}));*/
               /* $builder->add('locationCategories', new LocationCategoryNType(), array('label'=>'Stedets øvrige kategorier', 'multiple'=>true, 'expanded'=>true, 'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('loccat')->where('loccat.isTopcategory!=1')->orderBy('loccat.id', 'ASC');}));*/
        // Set the readableName and address fields to static when already filled-in.
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'setFieldsReadOnly'));
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event){
                $data = $event->getData();
                $form = $event->getForm();
                if (null === $data) {
                    return;
                }
                $form->get('locationCategories')->setData($data->getLocationCategories());
                $form->get('addressCityAndZip')->setData($data->getAddressZip());
            });
    }

    public function setFieldsReadOnly(FormEvent $event){
        // See injecting services: http://stackoverflow.com/questions/14356173/using-a-custom-service-in-a-form-type-in-symfony2
        $oioxml_json_string = file_get_contents('postnumre.json');
        $postalCodesAndcitiesJSONAssoc = json_decode($oioxml_json_string, true);

        $postalCodesAndcitiesAssoc = array();
        foreach($postalCodesAndcitiesJSONAssoc as $json_row){
            $postalCodesAndcitiesAssoc[$json_row['nr']] = $json_row['navn'] . ' ( ' . $json_row['nr'] .' )';
        }
        ksort($postalCodesAndcitiesAssoc);

        $data = $event->getData();
        $form = $event->getForm();

        if (($data->getReadableName() || ($data->getReadableName() !== '')) ) {
          /*  $form->add('location_top_category', new LocationCategoryNType(), array('required'=>true, 'label'=>'Top kategori', 'empty_value'=>'-- Vælg top-kategori --','multiple'=>false, 'expanded'=>false,'required'=>false,'read_only' => true, 'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('loccat')->where('loccat.isTopcategory=1')->orderBy('loccat.id', 'ASC');}));*/
           // $form->add('readableName', 'text', array('label'=>'Stedets navn', 'required'=>true, 'read_only' => true));
            $form->add('latitude', 'hidden', array('label'=>'Breddegrad', 'required'=>false))
            ->add('longitude', 'hidden', array('label'=>'Længdegrad', 'required'=>false))
            /*->add('addressZip', 'text', array('label'=>'Postnr.','read_only' => true))
            ->add('addressCity', 'text', array('label'=>'By', 'read_only' => true))*/
            ->add('addressStreet', 'text', array('label'=>'Gade/vej navn', 'required'=>true, 'property_path' => 'addressStreetAndExtd'))
            ->add('addressCityAndZip', 'choice', array('label'=>'Bynavn (postnr.)','choices'=>$postalCodesAndcitiesAssoc,'expanded' => false, 'multiple' => false, 'required'=>true));
        }
        else{
         /*   $form->add('location_top_category', new LocationCategoryNType(), array('cascade_validation' => true, 'label'=>'Stedets top kategori', 'empty_value'=>'-- Vælg top-kategori --','multiple'=>false, 'expanded'=>false,'required'=>true, 'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('loccat')->where('loccat.isTopcategory=1')->orderBy('loccat.id', 'ASC');}));*/
           // $form->add('readableName', 'text', array('label'=>'Stedets navn', 'required'=>true));
                $form->add('latitude', 'hidden', array('label'=>'Breddegrad','required'=>false))
                ->add('longitude', 'hidden', array('label'=>'Længdegrad','required'=>false))
                ->add('addressStreet', 'text', array('label'=>'Gade/vej navn','required'=>true))
                ->add('addressCityAndZip', 'choice', array('label'=>'Bynavn (postnr.)','choices'=>$postalCodesAndcitiesAssoc, 'expanded' => false, 'multiple' => false, 'required'=>true));
        }
        $form->add('locationCategories', new LocationCategoryNType(), array('label'=>'Stedets øvrige kategorier', 'multiple'=>true, 'expanded'=>true, 'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('loccat')->where('loccat.isTopcategory!=1')->orderBy('loccat.id', 'ASC');}));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Gladtur\TagBundle\Entity\Location',
                'cascade_validation' => true,
                'csrf_protection'   => false,
                ));
    }


    public function getName()
    {
        return 'gladturlocation';
    }
}
