<?php

namespace MediaBundle\Admin;

use AdminBundle\Admin\BaseAdmin as Admin;
use AdminBundle\Form\Type\UploadVichImageType;
use AppBundle\Services\ImageOptimizer;
use MediaBundle\Entity\MediaImage;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Sonata\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class MediaImageAdmin
 */
class MediaImageAdmin extends Admin
{
    /**
     * @var ImageOptimizer
     */
    protected $optimizer;

    /**
     * @var array
     */
    protected $datagridValues = [
        '_page' => 1,
        '_per_page' => 25,
        '_sort_by' => 'id',
        '_sort_order' => 'desc',
    ];

    public function setOptimizer(ImageOptimizer $optimizer)
    {
        $this->optimizer = $optimizer;
    }

    /**
     * @return mixed
     */
    protected function alterNewInstance(object $object): void
    {
        $object->setCreatedBy($this->getAdminUser());
    }

    protected function configurePersistentParameters(): array
    {
        if (!$this->hasRequest()) {
            return [];
        }

        $parameters = array_filter($this->getRequest()->query->all(), function ($param) {
            return !is_array($param);
        });

        return $parameters;
    }

    /**
     * @param MediaImage $object
     */
    public function prePersist(object $object): void
    {
        $this->preUpdate($object);
    }

    /**
     * @param MediaImage $object
     */
    public function preUpdate(object $object): void
    {
        $file = $object->getFile();
        if ($file) {
            [$width, $height] = getimagesize($file->getRealPath());
            if ($width && $height) {
                $object->setWidth($width);
                $object->setHeight($height);
            } else {
                $object->setWidth(null);
                $object->setHeight(null);
            }
            $object->setSize($file->getSize());
            $object->setMimeType($file->getMimeType());
        }
    }

    /**
     * @param MediaImage $object
     */
    public function postPersist(object $object): void
    {
        parent::postPersist($object);

        $this->postUpdate($object);
    }

    /**
     * @param MediaImage $object
     */
    public function postUpdate(object $object): void
    {
        if ($object->getFile()) {
            $this->optimizer->resize($object);
        }
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->remove('acl');

        $collection->add('preview', 'preview');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id', null, [
                'label' => 'media.fields.id',
            ])
            ->addIdentifier('title', null, [
                'label' => 'media.fields.file',
                'field' => 'title',
                'template' => '@Media/Admin/list_title.html.twig',
            ])
            ->addIdentifier('description', null, [
                'label' => 'media.fields.description',
                'template' => '@Media/Admin/list_media.html.twig',
            ])
            ->add('isActive', null, [
                'label' => 'media.fields.is_active',
                'editable' => true,
            ])
            ->add('createdAt', null, [
                'label' => 'media.fields.created_at',
            ])
            ->add('_action', 'actions', [
                'template' => isset($this->getPersistentParameters()['CKEditor']) ? '@Admin/Ckeditor/select.html.twig' : null,
                'actions' => [
                    'delete' => [],
                    'edit' => [],
                ],
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id', null, [
                'label' => 'media.fields.id',
                'show_filter' => true,
            ])
            ->add('description', null, [
                'label' => 'media.fields.description',
            ])
            ->add('isActive', null, [
                'label' => 'media.fields.is_active',
            ])
            ->add('createdAt', DateFilter::class, [
                'label' => 'media.fields.created_at',
                'field_type' => DateTimePickerType::class,
                'field_options' => ['format' => 'dd.MM.yyyy'],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('form_group.basic', ['class' => 'col-md-4', 'label' => false])
                ->add('description', TextType::class, [
                    'label' => 'media.fields.description',
                    'required' => false,
                ])
                ->add('file', UploadVichImageType::class, [
                    'label' => 'media.fields.file',
                    'preview_width' => 250,
                    'required' => false,
                ])
            ->end()
            ->with('form_group.basic2', ['class' => 'col-md-4', 'label' => false])
                ->add('mimeType', TextType::class, [
                    'label' => 'media.fields.mime_type',
                    'required' => false,
                    'attr' => ['readonly' => true],
                ])
                ->add('size', TextType::class, [
                    'label' => 'media.fields.size',
                    'required' => false,
                    'attr' => ['readonly' => true],
                ])
                ->add('width', TextType::class, [
                    'label' => 'media.fields.width',
                    'required' => false,
                    'attr' => ['readonly' => true],
                ])
                ->add('height', TextType::class, [
                    'label' => 'media.fields.height',
                    'required' => false,
                    'attr' => ['readonly' => true],
                ])
            ->end()
            ->with('form_group.additional', ['class' => 'col-md-4', 'label' => false])
                ->add('isActive', null, [
                    'label' => 'media.fields.is_active',
                    'required' => false,
                ])
                ->add('createdBy', ModelListType::class, [
                    'label' => 'media.fields.created_by',
                    'btn_edit' => false,
                    'btn_add' => false,
                    'required' => true,
                ])
                ->add('updatedAt', DateTimePickerType::class, [
                    'label' => 'media.fields.updated_at',
                    'format' => 'yyyy-MM-dd HH:mm',
                    'attr' => ['readonly' => true],
                ])
                ->add('createdAt', DateTimePickerType::class, [
                    'label' => 'media.fields.created_at',
                    'required' => true,
                    'format' => 'yyyy-MM-dd HH:mm',
                    'attr' => ['readonly' => true],
                ])
            ->end();
    }
}
