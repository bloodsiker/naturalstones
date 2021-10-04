<?php

namespace AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TextCounterType
 */
class TextCounterType extends AbstractType
{
    /**
     * @var array
     */
    private $defaultOptions = [
        'min'               => 0,
        'max'               => 255,
        'count_down'        => false,
        'count_spaces'      => false,
        'stop_at_maximum'   => false,
        'widget_type'       => 'character', // "character" or "word"
    ];

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults($this->defaultOptions);
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        foreach (array_keys($this->defaultOptions) as $option) {
            $view->vars[$option] = $options[$option];
        }
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
        return 'text_counter';
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return TextType::class;
    }
}
