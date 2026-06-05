<?php

namespace AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ColorPickerType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['widget'] = $options['widget'];
        $view->vars['configs'] = $options['configs'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'widget' => 'text',
                'configs' => [],
            ])
            ->setAllowedValues('widget', ['text', 'image']);
    }

    public function getBlockPrefix(): string
    {
        return 'colorpicker';
    }

    public function getParent(): ?string
    {
        return TextType::class;
    }
}