<?php

namespace AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Class CodeMirrorType
 */
class CodeMirrorType extends AbstractType
{

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
        return 'codemirror';
    }

    /**
     * @return null|string
     */
    public function getParent()
    {
        return TextareaType::class;
    }
}
