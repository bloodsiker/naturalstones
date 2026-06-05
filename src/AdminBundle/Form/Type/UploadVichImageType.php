<?php

namespace AdminBundle\Form\Type;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Vich\UploaderBundle\Handler\UploadHandler;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;
use Vich\UploaderBundle\Metadata\MetadataReader;
use Vich\UploaderBundle\Storage\StorageInterface;
use Vich\UploaderBundle\Util\ClassUtils;

class UploadVichImageType extends VichImageType
{
    public function __construct(
        StorageInterface $storage,
        UploadHandler $handler,
        PropertyMappingFactory $factory,
        ?PropertyAccessorInterface $propertyAccessor,
        private readonly MetadataReader $metadata,
    ) {
        parent::__construct($storage, $handler, $factory, $propertyAccessor);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'preview' => true,
            'required' => false,
            'label' => null,
            'mime_types' => ['image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml', 'image/webp'],
            'image_path' => null,
            'image_uri' => true,
            'allow_delete' => true,
            'download_uri' => true,
            'preview_width' => null,
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        if (!empty($options['required'])) {
            $options['required'] = false;
            $existingClass = $view->vars['attr']['class'] ?? '';
            $view->vars['attr']['class'] = $existingClass
                ? implode(',', [$existingClass, 'required'])
                : 'required';
        }

        if (!empty($options['mime_types'])) {
            $view->vars['attr']['accept'] = is_array($options['mime_types'])
                ? implode(', ', $options['mime_types'])
                : $options['mime_types'];
        }

        if (!$options['image_path']) {
            $object = $view->vars['object'];
            foreach ($this->metadata->getUploadableFields(ClassUtils::getClass($object)) as $attribute) {
                $getPath = 'get' . ucfirst($attribute['fileNameProperty']);
                $options['image_path'] = $object->$getPath();
            }
        }

        $view->vars['preview'] = $options['preview'];
        $view->vars['required'] = $options['required'];
        $view->vars['mime_types'] = $options['mime_types'];
        $view->vars['image_path'] = $options['image_path'];
        $view->vars['preview_width'] = $options['preview_width'];
    }

    public function getBlockPrefix(): string
    {
        return 'upload_vich_image';
    }
}