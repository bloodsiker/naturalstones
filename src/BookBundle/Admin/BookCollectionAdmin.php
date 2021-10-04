<?php

namespace BookBundle\Admin;

use AdminBundle\Admin\BaseAdmin as Admin;
use AdminBundle\Form\Type\TextCounterType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\CoreBundle\Form\Type\CollectionType;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * Class BookCollectionAdmin
 */
class BookCollectionAdmin extends Admin
{
    /**
     * @var array
     */
    protected $datagridValues = [
        '_page'       => 1,
        '_per_page'   => 25,
        '_sort_by'    => 'id',
        '_sort_order' => 'DESC',
    ];

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('preview', 'preview');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, [
                'label' => 'book_collection.fields.id',
            ])
            ->add('poster', null, [
                'label'     => 'book_collection.fields.poster',
                'template'  => 'BookBundle:Admin:list_fields.html.twig',
            ])
            ->addIdentifier('title', null, [
                'label' => 'book_collection.fields.title',
            ])
            ->add('isActive', null, [
                'label' => 'book_collection.fields.is_active',
                'editable'  => true,
            ])
            ->add('views', null, [
                'label' => 'book_collection.fields.views',
            ])
            ->add('createdAt', null, [
                'label' => 'book_collection.fields.created_at',
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'preview' => ['template' => 'BookBundle:CRUD:list__action_preview.html.twig'],
                    'edit' => [],
                ],
            ])
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title', null, [
                'label' => 'book_collection.fields.title',
            ])
            ->add('isActive', null, [
                'label' => 'book_collection.fields.is_active',
            ])
            ->add('createdAt', null, [
                'label' => 'book_collection.fields.created_at',
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $context = $this->getPersistentParameter('context');

        $formMapper
            ->with('form_group.basic', ['class' => 'col-md-8', 'name' => null])
                ->add('title', TextCounterType::class, [
                    'label' => 'book_collection.fields.title',
                ])
                ->add('description', CKEditorType::class, [
                    'label' => 'book_collection.fields.description',
                    'config_name' => 'advanced',
                    'required' => true,
                    'attr' => [
                        'rows' => 5,
                    ],
                ])
                ->add('collectionHasBook', CollectionType::class, [
                    'label' => 'book_collection.fields.books',
                    'required' => false,
                    'constraints' => new Valid(),
                    'by_reference' => false,
                ], [
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable' => 'orderNum',
                    'link_parameters' => ['context' => $context],
                    'admin_code' => 'sonata.admin.book_collection_has_book',
                ])
            ->end()
            ->with('form_group.additional', ['class' => 'col-md-4', 'name' => null])
                ->add('isActive', null, [
                    'label' => 'book_collection.fields.is_active',
                    'required' => false,
                ])
                ->add('poster', ModelListType::class, [
                    'label' => 'book_collection.fields.poster',
                    'required' => false,
                ])
                ->add('genres', ModelAutocompleteType::class, [
                    'label' => 'book_collection.fields.genres',
                    'required' => false,
                    'property' => 'name',
                    'multiple' => true,
                    'attr' => ['class' => 'form-control'],
                    'btn_catalogue' => $this->translationDomain,
                    'minimum_input_length' => 2,
                ])
                ->add('slug', TextType::class, [
                    'label' => 'book_collection.fields.slug',
                    'required' => false,
                    'attr' => ['readonly' => !$this->getSubject()->getId() ? false : true],
                ])
                ->add('views', IntegerType::class, [
                    'label' => 'book_collection.fields.views',
                    'required' => false,
                    'attr' => ['readonly' => true],
                ])
                ->add('updatedAt', DateTimePickerType::class, [
                    'label'     => 'book_collection.fields.updated_at',
                    'required' => false,
                    'format' => 'dd-MM-YYYY HH:mm',
                    'attr' => ['readonly' => true],
                ])
            ->end()
        ;
    }
}
