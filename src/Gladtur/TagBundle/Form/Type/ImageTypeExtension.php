<?php
namespace Gladtur\TagBundle\Form\Type;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Util\PropertyPath;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImageTypeExtension extends AbstractTypeExtension
{
/**
* Returns the name of the type being extended.
*
* @return string The name of the type being extended
*/
public function getExtendedType()
{
return 'file';
}

/**
* Add the image_path option
*
* @param OptionsResolverInterface $resolver
*/
public function setDefaultOptions(OptionsResolverInterface $resolver)
{
$resolver->setOptional(array('image_path'));
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
$propertyPath = new PropertyPath($options['image_path']);
$imageUrl = $propertyPath->getValue($parentData);
} else {
$imageUrl = null;
}

// set an "image_url" variable that will be available when rendering this field
$view->set('image_url', $imageUrl);
}
}
}