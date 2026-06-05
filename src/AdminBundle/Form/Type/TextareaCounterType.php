<?php

namespace AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextareaCounterType extends AbstractType
{
    /** @var array<string, mixed> */
    private array $defaultOptions = [
        'min' => 0,
        'max' => 255,
        'count_down' => false,
        'count_spaces' => false,
        'stop_at_maximum' => false,
        'widget_type' => 'character', // "character" or "word"
    ];

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults($this->defaultOptions);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        foreach (array_keys($this->defaultOptions) as $option) {
            $view->vars[$option] = $options[$option];
        }
    }

    public function getBlockPrefix(): string
    {
        return 'textarea_counter';
    }

    public function getParent(): ?string
    {
        return TextareaType::class;
    }
}