<?php

declare(strict_types=1);

namespace AppBundle\Block;

use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Block\Service\EditableBlockService;
use Sonata\BlockBundle\Form\Mapper\FormMapper;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Meta\MetadataInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\Form\Validator\ErrorElement;

abstract class AbstractEditableBlockService extends AbstractBlockService implements EditableBlockService
{
    public function configureCreateForm(FormMapper $form, BlockInterface $block): void
    {
        $this->configureEditForm($form, $block);
    }

    public function configureEditForm(FormMapper $form, BlockInterface $block): void
    {
    }

    public function validate(ErrorElement $errorElement, BlockInterface $block): void
    {
    }

    public function getMetadata(): MetadataInterface
    {
        $class = (new \ReflectionClass($this))->getShortName();
        $class = preg_replace('/BlockService$/', '', $class) ?? $class;
        $class = trim((string) preg_replace('/(?<!^)([A-Z])/', ' $1', $class));

        return new Metadata($class, null, null, null, [
            'class' => 'fa fa-cube',
        ]);
    }
}
