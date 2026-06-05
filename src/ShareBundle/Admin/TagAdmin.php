<?php

namespace ShareBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class TagAdmin
 */
class TagAdmin extends Admin
{
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
                        'name' => [
                            'label' => 'tag.fields.name',
                            'field_type' => TextType::class,
                            'required' => true,
                        ],
                    ],
                ])
            ->end()
            ->with('share.form_group.additional', ['class' => 'col-md-4', 'label' => false])
                ->add('slug', TextType::class, [
                    'label' => 'tag.fields.slug',
                    'required' => false,
                    'attr' => [
                        'readonly' => $this->getSubject()->getId() > 0,
                    ],
                ])
                ->add('isActive', CheckboxType::class, [
                    'label' => 'tag.fields.is_active',
                    'required' => false,
                ])
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id', null, [
                'label' => 'tag.fields.id',
            ])
            ->add('translations.name', null, [
                'label' => 'tag.fields.name',
            ])
            ->add('isActive', null, [
                'label' => 'tag.fields.is_active',
            ])
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id', null, [
                'label' => 'tag.fields.id',
            ])
            ->add('isActive', null, [
                'label' => 'tag.fields.is_active',
                'editable' => true,
            ])
            ->addIdentifier('name', null, [
                'label' => 'tag.fields.name',
                'field' => 'name',
            ])
            ->add('slug', null, [
                'label' => 'tag.fields.slug',
            ])
            ->add('_action', 'actions', [
                'actions' => ['edit' => []],
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('slug')
            ->add('isActive')
        ;
    }
}
