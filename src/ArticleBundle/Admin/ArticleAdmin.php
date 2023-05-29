<?php

namespace ArticleBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use AdminBundle\Admin\BaseAdmin as Admin;
use AppBundle\Traits\FixAdminFormTranslationDomainTrait;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Sonata\DatagridBundle\Filter\FilterInterface;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class ArticleAdmin
 */
class ArticleAdmin extends Admin
{
    use FixAdminFormTranslationDomainTrait;

    /**
     * @var array
     */
    protected $datagridValues = [
        '_page'       => 1,
        '_per_page'   => 25,
        '_sort_by'    => 'id',
        '_sort_order' => 'ASC',
    ];

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('acl');

        $collection->add('preview', 'preview');
    }

    /**
     * @return array
     */
    public function getPersistentParameters()
    {
        if (!$this->hasRequest()) {
            return [];
        }

        $parameters = array_filter($this->getRequest()->query->all(), function ($param) {
            return !is_array($param);
        });

        return $parameters;
    }

    /**
     * @param string $name
     *
     * @return null|string|void
     */
    public function getTemplate($name)
    {
        $parameters = $this->getPersistentParameters();
        if (in_array($name, ['list', 'edit']) && !empty($parameters['CKEditor'])) {
            return 'AdminBundle:Ckeditor:ajax.html.twig';
        }

        return parent::getTemplate($name);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, [
                'label' => 'article.fields.id',
            ])
            ->addIdentifier('title', null, [
                'label' => 'article.fields.title',
            ])
            ->add('category', null, [
                'label' => 'article.fields.category',
            ])
            ->add('createdAt', null, [
                'label' => 'article.fields.created_at',
            ])
            ->add('isActive', null, [
                'label' => 'article.fields.is_active',
                'editable'  => true,
            ])
            ->add('_action', 'actions', [
                'template' => isset($this->getPersistentParameters()['CKEditor']) ? 'AdminBundle:Ckeditor:select.html.twig' : null,
                'actions' => [
                    'edit'      => [],
                ],
            ]);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('translations.title', null, [
                'label' => 'article.fields.title',
            ])
            ->add('category', null, [
                'label' => 'article.fields.title',
            ])
            ->add('isActive', null, [
                'label' => 'article.fields.is_active',
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
                        'title' => [
                            'label' => 'article.fields.title',
                            'field_type' => TextType::class,
                            'required' => true,
                        ],
                        'shortDescription' => [
                            'label' => 'article.fields.short_description',
                            'field_type' => TextareaType::class,
                            'required' => false,
                            'attr' => [
                                'rows' => 3,
                            ],
                        ],
                        'description' => [
                            'label' => 'article.fields.description',
                            'field_type' => CKEditorType::class,
                            'config_name' => 'advanced',
                            'required' => false,
                        ],
                    ],
                ])
                ->add('slug', TextType::class, [
                    'label' => 'article.fields.slug',
                    'required' => false,
                    'attr' => ['readonly' => !$this->getSubject()->getId() ? false : true],
                ])
            ->end()
            ->with('form_group.additional', ['class' => 'col-md-4', 'name' => false])
                ->add('isActive', null, [
                    'label' => 'article.fields.is_active',
                    'required' => false,
                ])
                ->add('category', ModelListType::class, [
                    'label' => 'article.fields.category',
                    'btn_edit' => 'link_edit',
                    'required' => true,
                ])
                ->add('image', ModelListType::class, [
                    'label' => 'article.fields.image',
                    'btn_edit' => 'link_edit',
                    'required' => true,
                ])
                ->add('tags', ModelAutocompleteType::class, [
                    'label' => 'article.fields.tags',
                    'required' => false,
                    'property' => 'translations.name',
                    'multiple' => true,
                    'attr' => ['class' => 'form-control'],
                    'callback' => function($admin, $property, $value) {
                        $datagrid = $admin->getDatagrid();
                        if (!$datagrid->hasFilter($property)) {
                            throw new \RuntimeException(sprintf(
                                'To retrieve autocomplete items,'
                                .' you should add filter "%s" to "%s" in configureDatagridFilters() method.',
                                $property,
                                \get_class($admin)
                            ));
                        }
                        $filter = $datagrid->getFilter($property);
                        $filter->setCondition(FilterInterface::CONDITION_AND);

                        $datagrid->setValue($filter->getFormName(), null, $value);
                        $filterActive = $datagrid->getFilter('isActive');
                        $filterActive->setCondition(FilterInterface::CONDITION_AND);
                        $datagrid->setValue($filterActive->getFormName(), null, true);
                        $datagrid->setValue($datagrid->getFilter($property)->getFormName(), null, $value);
                    },
                    'btn_catalogue' => $this->translationDomain,
                    'minimum_input_length' => 2,
                ])
                ->add('views', IntegerType::class, [
                    'label' => 'article.fields.views',
                    'required' => false,
                    'attr' => ['readonly' => true],
                ])
                ->add('updatedAt', DateTimePickerType::class, [
                    'label'     => 'article.fields.updated_at',
                    'required' => false,
                    'format' => 'dd-MM-YYYY HH:mm',
                    'attr' => ['readonly' => true],
                ])
                ->add('createdAt', DateTimePickerType::class, [
                    'label'     => 'article.fields.created_at',
                    'required' => true,
                    'format' => 'YYYY-MM-dd HH:mm',
                    'attr' => ['readonly' => true],
                ])
            ->end();
    }
}
