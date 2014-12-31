<?php
namespace Gladtur\WebsiteBundle\Form\Type;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TextTypeExtension extends AbstractTypeExtension
{
    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return 'text';
    }

    /**
     * Add the image_path option
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(array('label_inner'));
    }

    /**
     * Pass the image url to the view
     *
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (array_key_exists('image_path', $options)) {
            $parentData = $form->getParent()->getData();

            if (null !== $parentData) {
                $propertyAccessor = PropertyAccess::getPropertyAccessor();

//$propertyPath = new PropertyPath($options['image_path']);
                $imageUrl = $propertyAccessor->getValue($parentData, $options['image_path']);
            } else {
                $imageUrl = null;
            }

// set an "image_url" variable that will be available when rendering this field
            /**
             * @var FormView $view
             */
            $view->vars['image_url']= $imageUrl;
            //$view->set('image_url', $imageUrl);
        }
        else{
            $view->vars['image_url'] = null;
            //$view->set('image_url', null);
        }
    }
}