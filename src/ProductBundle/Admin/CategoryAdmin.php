<?php

namespace ProductBundle\Admin;

use AdminBundle\Admin\BaseAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class CategoryAdmin
 */
class CategoryAdmin extends Admin
{
    protected $datagridValues = [
        '_page'       => 1,
        '_per_page'   => 25,
        '_sort_by'    => 'id',
        '_sort_order' => 'ASC',
    ];

    /**
     * {@inheritdoc}
     */
    public function setTranslationDomain($translationDomain)
    {
        $this->translationDomain = $translationDomain;
        $this->formOptions['translation_domain'] = $translationDomain;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, [
                'label' => 'category.fields.id',
            ])
            ->addIdentifier('name', null, [
                'label' => 'category.fields.name',
            ])
            ->add('slug', null, [
                'label' => 'category.fields.slug',
            ])
            ->add('type', 'choice', [
                'label' => 'category.fields.type',
                'choices' => array_flip($this->getTypes()),
                'catalogue' => $this->getTranslationDomain(),
            ])
            ->add('isActive', null, [
                'label' => 'category.fields.is_active',
                'editable'  => true,
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
                'label' => 'category.fields.name',
            ])
            ->add('slug', null, [
                'label' => 'category.fields.slug',
            ])
            ->add('type', null, [
                'label' => 'category.fields.type',
            ])
            ->add('isActive', null, [
                'label' => 'category.fields.is_active',
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
                    'label' => 'category.fields.name',
                ])
                ->add('slug', TextType::class, [
                    'label' => 'category.fields.slug',
                    'required' => false,
                    'attr' => ['readonly' => !$this->getSubject()->getId() ? false : true],
                ])
            ->end()
            ->with('form_group.additional', ['class' => 'col-md-4', 'name' => false])
                ->add('isActive', null, [
                    'label' => 'category.fields.is_active',
                    'required' => false,
                ])
                ->add('type', ChoiceType::class, [
                    'label' => 'category.fields.type',
                    'choices' => $this->getTypes(),
                    'required' => true,
                ])
            ->end();
    }

    private function getTypes()
    {
        $matchEntity = $this->getClass();
        $typesEntity = $matchEntity::getTypes();

        $typesChoice = [];
        foreach ($typesEntity as $key => $value) {
            $typesChoice["category.fields.types.".$value] = $key;
        }

        return $typesChoice;
    }
}
