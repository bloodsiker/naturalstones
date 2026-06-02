<?php

namespace AppBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use AdminBundle\Admin\BaseAdmin as Admin;
use AppBundle\Traits\FixAdminFormTranslationDomainTrait;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Valid;

class MenuSectionAdmin extends Admin
{
    use FixAdminFormTranslationDomainTrait;

    protected $datagridValues = [
        '_sort_by' => 'orderNum',
        '_sort_order' => 'DESC',
    ];

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id', null, ['label' => 'menu_section.fields.id'])
            ->addIdentifier('title', null, ['label' => 'menu_section.fields.title'])
            ->add('items', null, ['label' => 'menu_section.fields.items'])
            ->add('isActive', null, [
                'label' => 'menu_section.fields.is_active',
                'editable' => true,
            ])
            ->add('orderNum', null, [
                'label' => 'menu_section.fields.order_num',
                'editable' => true,
            ])
            ->add('_action', 'actions', [
                'actions' => ['edit' => []],
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('translations.title', null, ['label' => 'menu_section.fields.title'])
            ->add('isActive', null, ['label' => 'menu_section.fields.is_active']);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('form_group.basic', ['class' => 'col-md-8', 'name' => false])
                ->add('translations', TranslationsType::class, [
                    'translation_domain' => $this->getTranslationDomain(),
                    'label' => false,
                    'fields' => [
                        'title' => [
                            'label' => 'menu_section.fields.title',
                            'field_type' => TextType::class,
                            'required' => true,
                        ],
                    ],
                ])
                ->add('items', CollectionType::class, [
                    'label' => 'menu_section.fields.items',
                    'by_reference' => false,
                    'required' => false,
                    'constraints' => new Valid(),
                ], [
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable' => 'orderNum',
                    'admin_code' => 'app.admin.menu_item',
                ])
            ->end()
            ->with('form_group.additional', ['class' => 'col-md-4', 'name' => false])
                ->add('isActive', null, [
                    'label' => 'menu_section.fields.is_active',
                    'required' => false,
                ])
                ->add('orderNum', IntegerType::class, [
                    'label' => 'menu_section.fields.order_num',
                ])
            ->end();
    }
}
