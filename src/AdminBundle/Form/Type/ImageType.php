<?php

namespace AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType extends AbstractType
{
    public function getParent(): ?string
    {
        return FileType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'image';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if (!empty($options['required'])) {
            $options['required'] = false;
            $existingClass = $view->vars['attr']['class'] ?? '';
            $view->vars['attr']['class'] = $existingClass
                ? implode(',', [$existingClass, 'required'])
                : 'required';
        }

        $view->vars['required'] = $options['required'];
    }
}