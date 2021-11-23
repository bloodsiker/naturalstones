<?php

namespace MediaBundle\Admin;

use AdminBundle\Admin\BaseAdmin as Admin;
use AdminBundle\Form\Type\UploadVichImageType;
use MediaBundle\Twig\Extension\MediaExtension;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class MediaImageAdmin
 */
class MediaImageAdmin extends Admin
{
    /**
     * @var array
     */
    protected $datagridValues = [
        '_page'       => 1,
        '_per_page'   => 25,
        '_sort_by'    => 'id',
        '_sort_order' => 'desc',
    ];

    /**
     * @return mixed
     */
    public function getAdminUser()
    {
        return $this
            ->getConfigurationPool()
            ->getContainer()
            ->get('security.token_storage')
            ->getToken()
            ->getUser();
    }

    /**
     * @return mixed
     */
    public function getNewInstance()
    {
        $instance = parent::getNewInstance();
        $instance->setCreatedBy($this->getAdminUser());

        return $instance;
    }

    /**
     * @param MediaFile $object
     */
    public function prePersist($object)
    {
        $this->preUpdate($object);
    }

    /**
     * @param MediaFile $object
     */
    public function preUpdate($object)
    {
        $file = $object->getFile();
        if ($file) {
            list($width, $height) = getimagesize($file->getRealPath());
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
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
//        $collection->add('preview', 'preview');
        $collection->remove('acl');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, [
                'label' => 'media.fields.id',
            ])
            ->addIdentifier('title', null, [
                'label' => 'media.fields.file',
                'field' => 'title',
                'template' => 'MediaBundle:Admin:list_title.html.twig',
            ])
            ->addIdentifier('description', null, [
                'label' => 'media.fields.description',
                'template'  => 'MediaBundle:Admin:list_media.html.twig',
            ])
            ->add('isActive', null, [
                'label' => 'media.fields.is_active',
                'editable'  => true,
            ])
            ->add('createdAt', null, [
                'label' => 'media.fields.created_at',
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'delete' => [],
                    'edit' => [],
                ],
            ]);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('description', null, [
                'label' => 'media.fields.description',
            ])
            ->add('isActive', null, [
                'label' => 'media.fields.is_active',
            ])
            ->add('createdAt', null, [
                'label' => 'media.fields.created_at',
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form_group.basic', ['class' => 'col-md-4', 'label' => false])
                ->add('description', TextType::class, [
                    'label' => 'media.fields.description',
                    'required'  => false,
                ])
                ->add('file', UploadVichImageType::class, [
                    'label'     => 'media.fields.file',
                    'preview_width' => 250,
                    'required'  => false,
                ])
                ->add('createdBy', ModelListType::class, [
                    'label'    => 'media.fields.created_by',
                    'btn_edit' => false,
                    'btn_add' => false,
                    'required' => true,
                ])
            ->end()
            ->with('form_group.basic2', ['class' => 'col-md-4', 'label' => false])
                ->add('mimeType', TextType::class, [
                    'label' => 'media.fields.mime_type',
                    'required' => false,
                    'attr'  => ['readonly' => true],
                ])
                ->add('size', TextType::class, [
                    'label' => 'media.fields.size',
                    'required' => false,
                    'attr'  => ['readonly' => true],
                ])
                ->add('width', TextType::class, [
                    'label' => 'media.fields.width',
                    'required' => false,
                    'attr'  => ['readonly' => true],
                ])
                ->add('height', TextType::class, [
                    'label' => 'media.fields.height',
                    'required' => false,
                    'attr'  => ['readonly' => true],
                ])
            ->end()
            ->with('form_group.additional', ['class' => 'col-md-4', 'label' => false])
                ->add('isActive', null, [
                    'label'    => 'media.fields.is_active',
                    'required' => false,
                ])
                ->add('updatedAt', DateTimePickerType::class, [
                    'label'  => 'media.fields.updated_at',
                    'format' => 'YYYY-MM-dd HH:mm',
                    'attr'   => ['readonly' => true],
                ])
                ->add('createdAt', DateTimePickerType::class, [
                    'label'    => 'media.fields.created_at',
                    'required' => true,
                    'format'   => 'YYYY-MM-dd HH:mm',
                    'attr'     => ['readonly' => true],
                ])
            ->end();
    }
}
