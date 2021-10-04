<?php

namespace AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ColorPickerType
 */
class ColorPickerType extends AbstractType
{
    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['widget'] = $options['widget'];
        $view->vars['configs'] = $options['configs'];
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'widget'  => 'text',
                'configs' => [],
            ])
            ->setAllowedValues('widget', [
                'text',
                'image',
            ])
        ;
    }

    /**
     * NEXT_MAJOR: Remove when dropping Symfony <2.8 support.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'colorpicker';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return TextType::class;
    }
}
