<?php

namespace AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CodeMirrorType extends AbstractType
{
    public function getBlockPrefix(): string
    {
        return 'codemirror';
    }

    public function getParent(): ?string
    {
        return TextareaType::class;
    }
}