<?php

namespace ProductBundle\Admin;

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
            ->add('type', 'choice', [
                'label' => 'category.fields.type',
                'choices' => array_flip($this->getTypes()),
                'catalogue' => $this->getTranslationDomain(),
            ])
            ->add('isActive', null, [
                'label' => 'category.fields.is_active',
                'editable'  => true,
            ])
            ->add('orderNum', null, [
                'label' => 'category.fields.order_num',
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
            ->add('type', null, [
                'label' => 'category.fields.type',
            ], ChoiceType::class, [
                'choices' => $this->getTypes(),
                'choice_translation_domain' => $this->getTranslationDomain(),
                'expanded' => false,
                'multiple' => false,
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
                        'description' => [
                            'label' => 'category.fields.description',
                            'field_type' => CKEditorType::class,
                            'config_name' => 'advanced',
                            'required' => true,
                            'attr' => [
                                'rows' => 5,
                            ],
                        ],
                    ],
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
                ->add('image', ModelListType::class, [
                    'label' => 'category.fields.image',
                    'required' => true,
                ])
                ->add('type', ChoiceType::class, [
                    'label' => 'category.fields.type',
                    'choices' => $this->getTypes(),
                    'required' => true,
                ])
                ->add('orderNum', IntegerType::class, [
                    'label' => 'category.fields.order_num',
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
