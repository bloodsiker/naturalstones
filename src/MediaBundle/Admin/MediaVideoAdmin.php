<?php

namespace MediaBundle\Admin;

use AdminBundle\Admin\BaseAdmin as Admin;
use AdminBundle\Form\Type\UploadVichImageType;
use AppBundle\Services\ImageOptimizer;
use MediaBundle\Entity\MediaImage;
use MediaBundle\Entity\MediaVideo;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class MediaVideoAdmin
 */
class MediaVideoAdmin extends Admin
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
     * @return array
     */
    public function getPersistentParameters()
    {
        if (!$this->hasRequest()) {
            return [];
        }

        return array_filter($this->getRequest()->query->all(), function ($param) {
            return !is_array($param);
        });
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
     * @param MediaVideo $object
     */
    public function prePersist($object)
    {
        $this->preUpdate($object);
    }

    /**
     * @param MediaVideo $object
     */
    public function preUpdate($object)
    {
        if ($object->getPath()) {
            $infoFile = json_decode(file_get_contents(sprintf('https://www.youtube.com/oembed?url=%s&format=json', $object->getPath())), true);

            $object->setDescription($infoFile['title']);
            $object->setWidth($infoFile['height']);
            $object->setHeight($infoFile['width']);
            $object->setMimeType($infoFile['type']);
            $object->setThumb($infoFile['thumbnail_url']);
            $object->setSize(0);
            $object->setUpdatedAt(new \DateTime('now'));
        }
    }

    /**
     * @param MediaVideo $object
     */
    public function postPersist($object)
    {
        parent::postPersist($object);

        $this->postUpdate($object);
    }

    /**
     * @param MediaVideo $object
     */
    public function postUpdate($object)
    {
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
            ->addIdentifier('thumb', null, [
                'label' => 'media.fields.thumb',
                'template'  => 'MediaBundle:Admin:list_media.html.twig',
            ])
            ->addIdentifier('description', null, [
                'label' => 'media.fields.description',
            ])
            ->add('path', null, [
                'label' => 'media.fields.path',
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
                ->add('path', TextType::class, [
                    'label' => 'media.fields.path',
                    'required'  => true,
                ])
                ->add('description', TextType::class, [
                    'label' => 'media.fields.description',
                    'required'  => false,
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
                ->add('createdBy', ModelListType::class, [
                    'label'    => 'media.fields.created_by',
                    'btn_edit' => false,
                    'btn_add' => false,
                    'required' => true,
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
