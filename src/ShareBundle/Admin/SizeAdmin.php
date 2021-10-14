<?php

namespace ShareBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class SizeAdmin
 */
class SizeAdmin extends Admin
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
                    'label' => 'size.fields.name',
                    'required' => true,
                ])
            ->end()
            ->with('share.form_group.additional', ['class' => 'col-md-4', 'label' => false])
                ->add('isActive', CheckboxType::class, [
                    'label' => 'size.fields.is_active',
                    'required' => false,
                ])
                ->add('type', ChoiceType::class, [
                    'label' => 'size.fields.type',
                    'choices' => $this->getTypes(),
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
                'label' => 'size.fields.id',
            ])
            ->add('name', null, [
                'label' => 'size.fields.name',
            ])
            ->add('type', null, [
                'label' => 'size.fields.type',
            ], ChoiceType::class, [
                'choices' => $this->getTypes(),
                'choice_translation_domain' => $this->getTranslationDomain(),
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('isActive', null, [
                'label' => 'size.fields.is_active',
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
                'label' => 'size.fields.id',
            ])
            ->add('isActive', null, [
                'label' => 'size.fields.is_active',
            ])
            ->addIdentifier('name', null, [
                'label' => 'size.fields.name',
                'field' => 'name',
            ])
            ->add('type', 'choice', [
                'label' => 'size.fields.type',
                'choices' => $this->getTypes(),
                'catalogue' => $this->getTranslationDomain(),
                'editable'  => true,
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
            ->add('isActive')
        ;
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    private function getTypes()
    {
        $matchEntity = $this->getClass();

        return $matchEntity::getTypes();
    }
}
