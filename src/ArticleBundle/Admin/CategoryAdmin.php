<?php

namespace ArticleBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use AdminBundle\Admin\BaseAdmin as Admin;
use AppBundle\Traits\FixAdminFormTranslationDomainTrait;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class CategoryAdmin
 */
class CategoryAdmin extends Admin
{
    use FixAdminFormTranslationDomainTrait;

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
                'label' => 'category.fields.id',
            ])
            ->addIdentifier('name', null, [
                'label' => 'category.fields.name',
            ])
            ->add('slug', null, [
                'label' => 'category.fields.slug',
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
            ->add('translations.name', null, [
                'label' => 'category.fields.name',
            ])
            ->add('slug', null, [
                'label' => 'category.fields.slug',
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
                ->add('translations', TranslationsType::class, [
                    'translation_domain' => $this->translationDomain,
                    'label' => false,
                    'fields' => [
                        'name' => [
                            'label' => 'category.fields.name',
                            'field_type' => TextType::class,
                            'required' => true,
                        ],
                    ],
                ])
            ->end()
            ->with('form_group.additional', ['class' => 'col-md-4', 'name' => false])
                ->add('isActive', null, [
                    'label' => 'category.fields.is_active',
                    'required' => false,
                ])
                ->add('slug', TextType::class, [
                    'label' => 'category.fields.slug',
                    'required' => false,
                    'attr' => ['readonly' => !$this->getSubject()->getId() ? false : true],
                ])
            ->end();
    }
}
