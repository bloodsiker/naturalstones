<?php

namespace ShareBundle\Admin;

use AdminBundle\Form\Type\ColorPickerType;
use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class MetalAdmin
 */
class MetalAdmin extends Admin
{
    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by'      => 'id',
        '_sort_order'   => 'DESC',
    ];

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('acl');
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('share.form_group.basic', ['class' => 'col-md-8', 'label' => false])
                ->add('name', TextType::class, [
                    'label' => 'metal.fields.name',
                    'required' => true,
                ])
            ->end()
            ->with('share.form_group.additional', ['class' => 'col-md-4', 'label' => false])
                ->add('isActive', CheckboxType::class, [
                    'label' => 'metal.fields.is_active',
                    'required' => false,
                ])
                ->add('slug', TextType::class, [
                    'label' => 'metal.fields.slug',
                    'required' => false,
                    'attr'      => [
                        'readonly'  => $this->getSubject()->getId() > 0,
                    ],
                ])
            ->end()
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id', null, [
                'label' => 'metal.fields.id',
            ])
            ->add('name', null, [
                'label' => 'metal.fields.name',
            ])
            ->add('isActive', null, [
                'label' => 'metal.fields.is_active',
            ])
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, [
                'label' => 'metal.fields.id',
            ])
            ->addIdentifier('name', null, [
                'label' => 'metal.fields.name',
                'field' => 'name',
            ])
            ->add('slug', null, [
                'label' => 'metal.fields.slug',
            ])
            ->add('isActive', null, [
                'label' => 'metal.fields.is_active',
                'editable' => true,
            ])
            ->add('_action', 'actions', [
                'actions' => [
//                    'preview' => ['template' => 'ShareBundle:CRUD:list__action_preview.html.twig'],
                    'edit' => [],
                ],
            ]);
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('slug')
            ->add('isActive')
        ;
    }
}
