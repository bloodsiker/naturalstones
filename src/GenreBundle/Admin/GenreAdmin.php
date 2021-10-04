<?php

namespace GenreBundle\Admin;

use AdminBundle\Admin\BaseAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class GenreAdmin
 */
class GenreAdmin extends Admin
{
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
                'label' => 'genre.fields.id',
            ])
            ->addIdentifier('name', null, [
                'label' => 'genre.fields.name',
            ])
            ->add('parent', null, [
                'label' => 'genre.fields.parent',
            ])
            ->add('countBook', null, [
                'label' => 'genre.fields.count_book',
            ])
            ->add('isActive', null, [
                'label' => 'genre.fields.is_active',
                'editable'  => true,
            ])
            ->add('createdAt', null, [
                'label' => 'genre.fields.created_at',
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
            ->add('name', null, [
                'label' => 'genre.fields.name',
            ])
            ->add('parent', null, [
                'label' => 'genre.fields.parent',
            ])
            ->add('isActive', null, [
                'label' => 'genre.fields.is_active',
            ])
            ->add('createdAt', null, [
                'label' => 'genre.fields.created_at',
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form_group.basic', ['class' => 'col-md-8', 'name' => false])
                ->add('name', TextType::class, [
                    'label' => 'genre.fields.name',
                ])
                ->add('slug', TextType::class, [
                    'label' => 'genre.fields.slug',
                    'required' => false,
                    'attr' => ['readonly' => !$this->getSubject()->getId() ? false : true],
                ])
            ->end()
            ->with('form_group.additional', ['class' => 'col-md-4', 'name' => false])
                ->add('isActive', null, [
                    'label' => 'genre.fields.is_active',
                    'required' => false,
                ])
                ->add('parent', ModelListType::class, array(
                    'label' => 'genre.fields.parent',
                    'required' => false,
                ))
                ->add('countBook', IntegerType::class, [
                    'label' => 'genre.fields.count_book',
                    'required' => false,
                    'attr' => ['readonly' => true],
                ])
                ->add('createdAt', DateTimePickerType::class, [
                    'label'     => 'genre.fields.created_at',
                    'required' => true,
                    'format' => 'YYYY-MM-dd HH:mm',
                    'attr' => ['readonly' => true],
                ])
            ->end();
    }
}
