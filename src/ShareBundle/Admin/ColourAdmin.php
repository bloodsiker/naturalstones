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
 * Class ColourAdmin
 */
class ColourAdmin extends Admin
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
                    'label' => 'colour.fields.name',
                    'required' => true,
                ])
            ->end()
            ->with('share.form_group.additional', ['class' => 'col-md-4', 'label' => false])
                ->add('isActive', CheckboxType::class, [
                    'label' => 'colour.fields.is_active',
                    'required' => false,
                ])
                ->add('slug', TextType::class, [
                    'label' => 'colour.fields.slug',
                    'required' => false,
                    'attr'      => [
                        'readonly'  => $this->getSubject()->getId() > 0,
                    ],
                ])
                ->add('colour', ColorPickerType::class, [
                    'label' => 'colour.fields.colour',
                    'required' => true,
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
                'label' => 'colour.fields.id',
            ])
            ->add('name', null, [
                'label' => 'colour.fields.name',
            ])
            ->add('isActive', null, [
                'label' => 'colour.fields.is_active',
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
                'label' => 'colour.fields.id',
            ])
            ->add('colour', null, [
                'label' => 'colour.fields.colour',
                'template'  => 'ShareBundle:Admin:list_fields.html.twig',
            ])
            ->addIdentifier('name', null, [
                'label' => 'colour.fields.name',
                'field' => 'name',
            ])
            ->add('slug', null, [
                'label' => 'colour.fields.slug',
            ])
            ->add('isActive', null, [
                'label' => 'colour.fields.is_active',
            ])
            ->add('_action', 'actions', [
                'actions' => ['edit' => []],
            ])
        ;
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
