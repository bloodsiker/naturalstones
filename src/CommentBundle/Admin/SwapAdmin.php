<?php

namespace CommentBundle\Admin;

use AdminBundle\Admin\BaseAdmin as Admin;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class SwapAdmin
 */
class SwapAdmin extends Admin
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
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', null, [
                'label' => 'swap.fields.id',
            ])
            ->add('user', null, [
                'label' => 'swap.fields.created_by',
            ])
            ->add('userName', null, [
                'label' => 'swap.fields.name',
            ])
            ->add('isActive', null, [
                'label' => 'swap.fields.is_active',
                'editable'  => true,
            ])
            ->add('createdAt', null, [
                'label' => 'swap.fields.created_at',
            ])
            ->add('_action', 'actions', [
                'actions' => [
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
            ->add('isActive', null, [
                'label' => 'swap.fields.is_active',
            ])
            ->add('createdAt', null, [
                'label' => 'swap.fields.created_at',
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form_group.basic', ['class' => 'col-md-8', 'name' => false])
                ->add('userName', TextType::class, [
                    'label'    => 'swap.fields.name',
                    'required' => false,
                ])
                ->add('userEmail', TextType::class, [
                    'label'    => 'swap.fields.email',
                    'required' => false,
                ])
                ->add('content', CKEditorType::class, [
                    'label' => 'swap.fields.content',
                    'required' => true,
                    'attr' => [
                        'rows' => 5,
                    ],
                ])
            ->end()
            ->with('form_group.additional', ['class' => 'col-md-4', 'name' => false])
                ->add('isActive', null, [
                    'label'    => 'swap.fields.is_active',
                    'required' => false,
                ])
                ->add('user', ModelListType::class, [
                    'label'    => 'swap.fields.created_by',
                    'required' => false,
                ])
                ->add('createdAt', DateTimePickerType::class, [
                    'label'     => 'swap.fields.created_at',
                    'required'  => true,
                    'format'    => 'YYYY-MM-dd HH:mm',
                    'attr'      => ['readonly' => true],
                ])
            ->end();
    }
}
