<?php

namespace InformationBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use AdminBundle\Admin\BaseAdmin as Admin;
use AppBundle\Traits\FixAdminFormTranslationDomainTrait;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class InformationAdmin
 */
class InformationAdmin extends Admin
{
    use FixAdminFormTranslationDomainTrait;

    /**
     * @var array
     */
    protected $datagridValues = [
        '_page'       => 1,
        '_per_page'   => 25,
        '_sort_by'    => 'id',
        '_sort_order' => 'ASC',
    ];

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, [
                'label' => 'information.fields.id',
            ])
            ->addIdentifier('title', null, [
                'label' => 'information.fields.title',
            ])
            ->add('startedAt', null, [
                'label' => 'information.fields.started_at',
            ])
            ->add('finishedAt', null, [
                'label' => 'information.fields.finished_at',
            ])
            ->add('isActive', null, [
                'label' => 'information.fields.is_active',
                'editable'  => true,
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'edit'      => [],
                ],
            ]);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
//            ->add('title', null, [
//                'label' => 'information.fields.title',
//            ])
            ->add('isActive', null, [
                'label' => 'information.fields.is_active',
            ])
            ->add('startedAt', DateFilter::class, [
                'label'         => 'information.fields.started_at',
                'field_type'    => DateTimePickerType::class,
                'field_options' => ['format' => 'dd.MM.yyyy'],
            ])
            ->add('finishedAt', DateFilter::class, [
                'label'         => 'information.fields.finished_at',
                'field_type'    => DateTimePickerType::class,
                'field_options' => ['format' => 'dd.MM.yyyy'],
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
                        'title' => [
                            'label' => 'information.fields.title',
                            'field_type' => TextType::class,
                            'required' => true,
                        ],
                        'description' => [
                            'label' => 'information.fields.description',
                            'field_type' => CKEditorType::class,
                            'config_name' => 'advanced',
                            'required' => false,
                        ],
                        'url' => [
                            'label' => 'information.fields.url',
                            'field_type' => TextType::class,
                            'required' => false,
                        ],
                    ],
                ])
            ->end()
            ->with('form_group.additional', ['class' => 'col-md-4', 'name' => false])
                ->add('isActive', null, [
                    'label' => 'information.fields.is_active',
                    'required' => false,
                ])
                ->add('startedAt', DateTimePickerType::class, [
                    'label'     => 'information.fields.started_at',
                    'required' => false,
                    'format' => 'YYYY-MM-dd HH:mm',
                ])
                ->add('finishedAt', DateTimePickerType::class, [
                    'label'     => 'information.fields.finished_at',
                    'required' => false,
                    'format' => 'YYYY-MM-dd HH:mm',
                ])
                ->add('createdAt', DateTimePickerType::class, [
                    'label'     => 'information.fields.created_at',
                    'required' => true,
                    'format' => 'YYYY-MM-dd HH:mm',
                    'attr' => ['readonly' => true],
                ])
            ->end();
    }
}
