<?php

namespace MainImageBundle\Admin;

use AdminBundle\Admin\BaseAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class MainImageAdmin
 */
class MainImageAdmin extends Admin
{
    /**
     * @var array
     */
    protected $datagridValues = [
        '_page'       => 1,
        '_per_page'   => 25,
        '_sort_by'    => 'orderNum',
        '_sort_order' => 'ASC',
    ];

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('moveup', $this->getRouterIdParameter().'/move-up');
        $collection->add('movedown', $this->getRouterIdParameter().'/move-down');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, [
                'label' => 'main_image.fields.id',
            ])
            ->add('image', null, [
                'label'     => 'main_image.fields.image',
                'template'  => 'MainImageBundle:Admin:list_fields.html.twig',
            ])
            ->addIdentifier('title', null, [
                'label' => 'main_image.fields.title',
            ])
            ->add('isActive', null, [
                'label' => 'main_image.fields.is_active',
                'editable'  => true,
            ])
            ->add('createdAt', null, [
                'label' => 'main_image.fields.created_at',
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'move_up'   => ['template' => 'AdminBundle:CRUD:list__action_move_up.html.twig'],
                    'order_num' => ['template' => 'AdminBundle:CRUD:list__action_order_num.html.twig'],
                    'move_down' => ['template' => 'AdminBundle:CRUD:list__action_move_down.html.twig'],
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
            ->add('isActive', null, [
                'label' => 'main_image.fields.is_active',
            ])
            ->add('createdAt', null, [
                'label' => 'main_image.fields.created_at',
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form_group.basic', ['class' => 'col-md-8', 'name' => false])
                ->add('title', TextType::class, [
                    'label' => 'main_image.fields.title',
                    'required' => false,
                ])
                ->add('description', TextareaType::class, [
                    'label' => 'main_image.fields.description',
                    'required' => false,
                    'attr' => [
                        'rows' => 5,
                    ],
                ])
            ->end()
            ->with('form_group.additional', ['class' => 'col-md-4', 'name' => false])
                ->add('isActive', null, [
                    'label' => 'main_image.fields.is_active',
                    'required' => false,
                ])
                ->add('image', ModelListType::class, [
                    'label' => 'main_image.fields.image',
                    'required' => false,
                ])
                ->add('orderNum', null, [
                    'label' => 'main_image.fields.order_num',
                    'required' => false,
                ])
                ->add('createdAt', DateTimePickerType::class, [
                    'label'     => 'main_image.fields.created_at',
                    'required' => true,
                    'format' => 'YYYY-MM-dd HH:mm',
                    'attr' => ['readonly' => true],
                ])
            ->end();
    }
}
