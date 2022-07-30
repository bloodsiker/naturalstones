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
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class StoneAdmin
 */
class StoneAdmin extends Admin
{
    use FixAdminFormTranslationDomainTrait;

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
        $collection->remove('acl');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, [
                'label' => 'stone.fields.id',
            ])
            ->add('image', null, [
                'label'     => 'stone.fields.image',
                'template'  => 'ShareBundle:Admin:list_fields.html.twig',
            ])
            ->addIdentifier('name', null, [
                'label' => 'stone.fields.name',
            ])
            ->add('isActive', null, [
                'label' => 'stone.fields.is_active',
                'editable'  => true,
            ])
            ->add('isShowMain', null, [
                'label' => 'stone.fields.is_show_main',
                'editable'  => true,
            ])
            ->add('slug', null, [
                'label' => 'stone.fields.slug',
            ])
            ->add('createdAt', null, [
                'label' => 'stone.fields.created_at',
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'preview' => ['template' => 'ShareBundle:CRUD:list__action_preview.html.twig'],
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
            ->add('name', null, [
                'label' => 'stone.fields.name',
            ])
            ->add('isActive', null, [
                'label' => 'stone.fields.is_active',
            ])
            ->add('isShowMain', null, [
                'label' => 'stone.fields.is_show_main',
            ])
            ->add('createdAt', null, [
                'label' => 'stone.fields.created_at',
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form_group.basic', ['class' => 'col-md-8', 'name' => false])
                ->add('translations', TranslationsType::class, [
                    'translation_domain' => $this->translationDomain,
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
                ->add('image', ModelListType::class, [
                    'label' => 'stone.fields.image',
                    'required' => true,
                ])
                ->add('zodiacs', ModelAutocompleteType::class, [
                    'label' => 'stone.fields.zodiacs',
                    'required' => false,
                    'property' => 'name',
                    'multiple' => true,
                    'attr' => ['class' => 'form-control'],
                    'btn_catalogue' => $this->translationDomain,
                    'minimum_input_length' => 2,
                ])
                ->add('createdAt', DateTimePickerType::class, [
                    'label'     => 'stone.fields.created_at',
                    'required' => true,
                    'format' => 'YYYY-MM-dd HH:mm',
                    'attr' => ['readonly' => true],
                ])
            ->end();
    }
}
