<?php

namespace WheelSpinBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use AdminBundle\Admin\DatagridValuesTrait;
use AppBundle\Traits\FixAdminFormTranslationDomainTrait;
use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class WheelSpinOptionAdmin
 */
class WheelSpinOptionAdmin extends Admin
{
    use FixAdminFormTranslationDomainTrait;
    use DatagridValuesTrait;

    /**
     * @var array
     */
    protected $datagridValues = [
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

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('share.form_group.basic', ['class' => 'col-md-8', 'label' => false])
                ->add('translations', TranslationsType::class, [
                    'translation_domain' => $this->getTranslationDomain(),
                    'label' => false,
                    'fields' => [
                        'title' => [
                            'label' => 'wheel_spin_option.fields.title',
                            'field_type' => TextType::class,
                            'required' => false,
                        ],
                    ],
                ])
            ->end()
            ->with('share.form_group.additional', ['class' => 'col-md-4', 'label' => false])
                ->add('isActive', CheckboxType::class, [
                    'label' => 'wheel_spin_option.fields.is_active',
                    'required' => false,
                ])
                ->add('product', ModelListType::class, [
                    'label' => 'wheel_spin_option.fields.product',
                    'btn_edit' => 'btn_edit',
                    'required' => true,
                ])
                ->add('image', ModelListType::class, [
                    'label' => 'wheel_spin_option.fields.image',
                    'required' => true,
                ])
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id', null, [
                'label' => 'wheel_spin_option.fields.id',
            ])
            ->add('translations.title', null, [
                'label' => 'wheel_spin_option.fields.title',
            ])
            ->add('isActive', null, [
                'label' => 'wheel_spin_option.fields.is_active',
            ])
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id', null, [
                'label' => 'wheel_spin_option.fields.id',
            ])
            ->add('image', null, [
                'label' => 'wheel_spin_option.fields.image',
                'template' => '@WheelSpin/Admin/list_fields.html.twig',
            ])
            ->add('product', null, [
                'label' => 'wheel_spin_option.fields.product',
            ])
            ->addIdentifier('title', null, [
                'label' => 'wheel_spin_option.fields.title',
                'field' => 'name',
            ])
            ->add('isActive', null, [
                'label' => 'wheel_spin_option.fields.is_active',
                'editable' => true,
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => [],
                ],
            ]);
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('isActive')
        ;
    }
}
