<?php

namespace ShareBundle\Admin;

use AdminBundle\Admin\DatagridValuesTrait;
use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class SizeAdmin
 */
class SizeAdmin extends Admin
{
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

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
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
                'field_type' => ChoiceType::class,
                'field_options' => [
                    'choices' => $this->getTypes(),
                    'choice_translation_domain' => $this->getTranslationDomain(),
                    'expanded' => false,
                    'multiple' => false,
                ],
            ])
            ->add('isActive', null, [
                'label' => 'size.fields.is_active',
            ])
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
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
                'editable' => true,
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
