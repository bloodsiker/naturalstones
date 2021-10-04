<?php

namespace PageBundle\Admin;

use AdminBundle\Form\Type\CodeMirrorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * Class SiteVariableAdmin
 */
class SiteVariableAdmin extends AbstractAdmin
{
    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('acl');
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->add('isActive', null, [
                'label' => 'list.label_active',
                'editable' => true,
            ])
            ->addIdentifier('name', null, [
                'label' => 'list.label_name',
            ])
            ->add('placement', 'text', [
                'label' => 'list.label_placement',
                'template'  => 'PageBundle:Admin:list.html.twig',
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'edit'      => [],
                ],
            ])
        ;
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('name', null, [
                'label' => 'filter.label_name',
            ])
            ->add('placement', null, [
                'label' => 'filter.label_placement',
            ])
            ->add('isActive', null, [
                'label' => 'filter.label_active',
            ])
        ;
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->with('site_variable_form_group.basic', ['class' => 'col-md-8', 'label' => false])
                ->add('name', null, [
                    'label' => 'form.label_name',
                    'required' => true,
                ])
                ->add('value', CodeMirrorType::class, [
                    'label' => 'form.label_value',
                    'required' => false,
                    'attr' => [
                        'rows' => 24,
                    ],
                ])
            ->end()
            ->with('site_variable_form_group.advanced', ['class' => 'col-md-4', 'label' => false])
                ->add('placement', ModelType::class, [
                    'label' => 'form.label_placement',
                    'translation_domain' => 'SonataPageBundle',
                    'required' => true,
                    'btn_add' => false,
                ])
                ->add('isActive', CheckboxType::class, [
                    'label' => 'form.label_active',
                    'required' => false,
                ])
                ->add('createdAt', null, [
                    'label' => 'form.label_created_at',
                    'widget' => 'single_text',
                    'format' => 'eeee dd.MM.yyyy, HH:mm:ss',
                    'attr' => [
                        'readonly' => true,
                    ],
                ])
                ->add('modifiedAt', null, [
                    'label' => 'form.label_modified_at',
                    'widget' => 'single_text',
                    'format' => 'eeee dd.MM.yyyy, HH:mm:ss',
                    'attr' => [
                        'readonly' => true,
                    ],
                ])
            ->end()
        ;
    }
}
