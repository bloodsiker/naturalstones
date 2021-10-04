<?php

namespace AdminBundle\Form\Type;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ImageType
 * @package AdminBundle\Form\Type
 */
class ImageType extends AbstractType
{
    /**
     * @return string
     */
    public function getParent()
    {
        return 'file';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'image';
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'required' => false,
        ));
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (isset($options['required']) && !empty($options['required'])) {
            $options['required'] = false;
            if (!$view->vars['attr']['class']) {
                $view->vars['attr']['class'] = 'required';
            } else {
                $view->vars['attr']['class'] = implode(',', [$view->vars['attr']['class'], 'required']);
            }
        }

        $view->vars['required'] = $options['required'];
    }
}
