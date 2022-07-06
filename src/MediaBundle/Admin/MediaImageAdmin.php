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
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
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
        '_page'       => 1,
        '_per_page'   => 25,
        '_sort_by'    => 'id',
        '_sort_order' => 'desc',
    ];

    public function setOptimizer(ImageOptimizer $optimizer)
    {
        $this->optimizer = $optimizer;
    }

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
     * @return array
     */
    public function getPersistentParameters()
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
     * @param string $name
     *
     * @return null|string|void
     */
    public function getTemplate($name)
    {
        $parameters = $this->getPersistentParameters();
        if (in_array($name, ['list', 'edit']) && !empty($parameters['CKEditor'])) {
            return 'AdminBundle:Ckeditor:ajax.html.twig';
        }

        return parent::getTemplate($name);
    }

    /**
     * @param MediaImage $object
     */
    public function prePersist($object)
    {
        $this->preUpdate($object);
    }

    /**
     * @param MediaImage $object
     */
    public function preUpdate($object)
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
    public function postPersist($object)
    {
        parent::postPersist($object);

        $this->postUpdate($object);
    }

    /**
     * @param MediaImage $object
     */
    public function postUpdate($object)
    {
        if ($object->getFile()) {
            $this->optimizer->resize($object);
        }
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
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
                'template' => isset($this->getPersistentParameters()['CKEditor']) ? 'AdminBundle:Ckeditor:select.html.twig' : null,
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
                'field_type'    => DateTimePickerType::class,
                'field_options' => array('format' => 'dd.MM.yyyy'),
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
