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

/**
 * Class UploadVichImageType
 */
class UploadVichImageType extends VichImageType
{
    /**
     * @var MetadataReader
     */
    private $metadata;

    /**
     * UploadVichImageType constructor.
     *
     * @param StorageInterface               $storage
     * @param UploadHandler                  $handler
     * @param PropertyMappingFactory         $factory
     * @param PropertyAccessorInterface|null $propertyAccessor
     * @param MetadataReader                 $metadata
     */
    public function __construct(
        StorageInterface $storage,
        UploadHandler $handler,
        PropertyMappingFactory $factory,
        PropertyAccessorInterface $propertyAccessor = null,
        MetadataReader $metadata
    ) {
        parent::__construct($storage, $handler, $factory, $propertyAccessor);

        $this->metadata = $metadata;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'preview'           => true,
            'required'          => false,
            'label'             => null,
            'mime_types'        => ['image/png', 'image/jpeg', 'image/jpg'],
            'image_path'        => null,
            'image_uri'         => true,
            'allow_delete'      => true,
            'download_uri'      => true,
            'preview_width'     => null,
        ]);
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        if (isset($options['required']) && !empty($options['required'])) {
            $options['required'] = false;
            if (!$view->vars['attr']['class']) {
                $view->vars['attr']['class'] = 'required';
            } else {
                $view->vars['attr']['class'] = implode(',', [$view->vars['attr']['class'], 'required']);
            }
        }

        if (isset($options['mime_types']) && !empty($options['mime_types'])) {
            if (is_array($options['mime_types'])) {
                $view->vars['attr']['accept'] =  implode(', ', $options['mime_types']);
            } else {
                $view->vars['attr']['accept'] = $options['mime_types'];
            }
        }

        if (!$options['image_path']) {
            $object = $view->vars['object'];

            if ($data = $this->metadata->getUploadableFields(ClassUtils::getClass($object))) {
                foreach ($data as $attribute) {
                    $getPath = 'get'.ucfirst($attribute['fileNameProperty']);
                    $options['image_path'] = $object->$getPath();
                }
            }
        }

        $view->vars['preview']          = $options['preview'];
        $view->vars['required']         = $options['required'];
        $view->vars['mime_types']       = $options['mime_types'];
        $view->vars['image_path']       = $options['image_path'];
        $view->vars['preview_width']    = $options['preview_width'];
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'upload_vich_image';
    }
}