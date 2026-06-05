<?php

namespace ShareBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use AdminBundle\Admin\BaseAdmin as Admin;
use AppBundle\Traits\FixAdminFormTranslationDomainTrait;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\Form\Type\CollectionType;
use Sonata\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * Class StoneAdmin
 */
class StoneAdmin extends Admin
{
    use FixAdminFormTranslationDomainTrait;

    protected $datagridValues = [
        '_page' => 1,
        '_per_page' => 25,
        '_sort_by' => 'id',
        '_sort_order' => 'DESC',
    ];

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->remove('acl');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id', null, [
                'label' => 'stone.fields.id',
            ])
            ->add('image', null, [
                'label' => 'stone.fields.image',
                'template' => '@Share/Admin/list_fields.html.twig',
            ])
            ->addIdentifier('name', null, [
                'label' => 'stone.fields.name',
            ])
            ->add('isActive', null, [
                'label' => 'stone.fields.is_active',
                'editable' => true,
            ])
            ->add('isShowMain', null, [
                'label' => 'stone.fields.is_show_main',
                'editable' => true,
            ])
            ->add('isShowConstructor', null, [
                'label' => 'stone.fields.is_show_constructor',
                'editable' => true,
            ])
            ->add('slug', null, [
                'label' => 'stone.fields.slug',
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'preview' => ['template' => '@Share/CRUD/list__action_preview.html.twig'],
                    'edit' => [],
                ],
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('translations.name', null, [
                'label' => 'stone.fields.name',
            ])
            ->add('isActive', null, [
                'label' => 'stone.fields.is_active',
            ])
            ->add('isShowMain', null, [
                'label' => 'stone.fields.is_show_main',
            ])
            ->add('isShowConstructor', null, [
                'label' => 'stone.fields.is_show_constructor',
            ])
            ->add('createdAt', null, [
                'label' => 'stone.fields.created_at',
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $context = $this->getPersistentParameter('context');

        $formMapper
            ->with('stone.tab.stone', ['tab' => true])
                ->with('form_group.basic', ['class' => 'col-md-8', 'name' => false])
                    ->add('translations', TranslationsType::class, [
                        'translation_domain' => $this->getTranslationDomain(),
                        'label' => false,
                        'fields' => [
                            'name' => [
                                'label' => 'stone.fields.name',
                                'field_type' => TextType::class,
                                'required' => true,
                            ],
                            'description' => [
                                'label' => 'stone.fields.description',
                                'field_type' => CKEditorType::class,
                                'config_name' => 'advanced',
                                'required' => false,
                                'attr' => [
                                    'rows' => 5,
                                ],
                            ],
                        ],
                    ])
                    ->add('slug', TextType::class, [
                        'label' => 'stone.fields.slug',
                        'required' => false,
                        'attr' => ['readonly' => !$this->getSubject()->getId() ? false : true],
                    ])
                ->end()
                ->with('form_group.additional', ['class' => 'col-md-4', 'name' => false])
                    ->add('isActive', null, [
                        'label' => 'stone.fields.is_active',
                        'required' => false,
                    ])
                    ->add('isShowMain', null, [
                        'label' => 'stone.fields.is_show_main',
                        'required' => false,
                    ])
                    ->add('isShowConstructor', null, [
                        'label' => 'stone.fields.is_show_constructor',
                        'required' => false,
                    ])
                    ->add('image', ModelListType::class, [
                        'label' => 'stone.fields.image',
                        'required' => true,
                    ])
                    ->add('zodiacs', ModelAutocompleteType::class, [
                        'label' => 'stone.fields.zodiacs',
                        'required' => false,
                        'property' => 'translations.name',
                        'multiple' => true,
                        'attr' => ['class' => 'form-control'],
                        'minimum_input_length' => 2,
                    ])
                    ->add('createdAt', DateTimePickerType::class, [
                        'label' => 'stone.fields.created_at',
                        'required' => true,
                        'format' => 'yyyy-MM-dd HH:mm',
                        'attr' => ['readonly' => true],
                    ])
                ->end()
            ->end()
            ->with('stone.tab.stone_constructor', ['tab' => true])
                ->with('form_group.product_option', ['class' => 'col-md-12', 'label' => null])
                    ->add('stoneHasConstructor', CollectionType::class, [
                        'label' => 'stone.fields.stone_constructor',
                        'required' => false,
                        'constraints' => new Valid(),
                        'by_reference' => false,
                    ], [
                        'edit' => 'inline',
                        'inline' => 'table',
                        'sortable' => 'orderNum',
                        'link_parameters' => ['context' => $context],
                        'admin_code' => 'share.admin.stone_has_constructor',
                    ])
                ->end()
            ->end();
    }
}
