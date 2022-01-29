<?php

namespace ProductBundle\Admin;

use AdminBundle\Admin\BaseAdmin as Admin;
use AdminBundle\Form\Type\TextCounterType;
use AppBundle\Traits\FixAdminFormTranslationDomainTrait;
use Doctrine\ORM\EntityManager;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Sonata\Form\Type\CollectionType;
use Sonata\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * Class ProductAdmin
 */
class ProductAdmin extends Admin
{
    use FixAdminFormTranslationDomainTrait;
    /**
     * @var EntityManager $entityManager
     */
    protected $entityManager;

    /**
     * @var array
     */
    protected $datagridValues = [
        '_page'       => 1,
        '_per_page'   => 25,
        '_sort_by'    => 'id',
        '_sort_order' => 'DESC',
    ];

    /**
     * @param EntityManager $entityManager
     *
     * @return EntityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        return $this->entityManager = $entityManager;
    }

    /**
     * @param  object  $object
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function postPersist($object)
    {
        $object->setProductGroup($object->getId());
        $this->entityManager->persist($object);
        $this->entityManager->flush();
    }

    /**
     * @return array
     */
    public function getFormTheme()
    {
        return array_merge(
            parent::getFormTheme(),
            ['ProductBundle:Form:admin_fields.html.twig']
        );
    }

    /**
     * @param ErrorElement $errorElement
     * @param mixed        $object
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('description')
                ->addConstraint(new NotNull())
            ->end()
            ->with('category')
                ->addConstraint(new NotNull())
            ->end()
        ;
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('acl');

        $collection->add('preview', 'preview');
        $collection->add('clone', $this->getRouterIdParameter().'/clone');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, [
                'label' => 'product.fields.id',
            ])
            ->add('image', null, [
                'label'     => 'product.fields.image',
                'template'  => 'ProductBundle:Admin:list_fields.html.twig',
            ])
            ->addIdentifier('name', null, [
                'label' => 'product.fields.name',
                'template' => 'ProductBundle:Admin:list_fields.html.twig',
            ])
            ->add('category', null, [
                'label' => 'product.fields.category',
            ])
            ->add('colours', null, [
                'label' => 'product.fields.colours',
                'template'  => 'ProductBundle:Admin:list_fields.html.twig',
            ])
            ->add('size', null, [
                'label' => 'product.fields.size',
                'template'  => 'ProductBundle:Admin:list_fields.html.twig',
            ])
            ->add('price', null, [
                'label' => 'product.fields.price',
            ])
            ->add('discount', null, [
                'label' => 'product.fields.discount',
            ])
            ->add('isActive', null, [
                'label' => 'product.fields.is_active',
                'editable'  => true,
            ])
            ->add('isAvailable', null, [
                'label' => 'product.fields.is_available',
                'editable'  => true,
            ])
            ->add('isMainProduct', null, [
                'label' => 'product.fields.is_main_product',
            ])
            ->add('orderNum', null, [
                'label' => 'product.fields.order_num',
                'editable'  => true,
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'preview' => ['template' => 'ProductBundle:CRUD:list__action_preview.html.twig'],
                    'edit' => [],
                    'clone'   => ['template' => 'ProductBundle:CRUD:list__action_clone.html.twig'],
                ],
            ])
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, [
                'label' => 'product.fields.name',
            ])
            ->add('category', null, [
                'label' => 'product.fields.category',
            ])
            ->add('metals', null, [
                'label' => 'product.fields.metals',
            ])
            ->add('colours', null, [
                'label' => 'product.fields.colours',
            ])
            ->add('price', null, [
                'label' => 'product.fields.price',
            ])
            ->add('tags', null, [
                'label' => 'product.fields.tags',
            ])
            ->add('size', null, [
                'label' => 'product.fields.size',
            ])
            ->add('isActive', null, [
                'label' => 'product.fields.is_active',
            ])
            ->add('isAvailable', null, [
                'label' => 'product.fields.is_available',
            ])
            ->add('isMainProduct', null, [
                'label' => 'product.fields.is_main_product',
            ])
            ->add('productGroup', null, [
                'label' => 'product.fields.product_group',
            ])
            ->add('createdAt', DateFilter::class, [
                'label'         => 'product.fields.created_at',
                'field_type'    => DateTimePickerType::class,
                'field_options' => ['format' => 'dd.MM.yyyy'],
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $context = $this->getPersistentParameter('context');

        $formMapper
            ->with('product.tab.product', ['tab' => true])
                ->with('form_group.basic', ['class' => 'col-md-8', 'name' => null])
                    ->add('name', TextCounterType::class, [
                        'label' => 'product.fields.name',
                    ])
                    ->add('description', CKEditorType::class, [
                        'label' => 'product.fields.description',
                        'config_name' => 'advanced',
                        'required' => true,
                        'attr' => [
                            'rows' => 5,
                        ],
                    ])
                    ->add('instagram_link', UrlType::class, [
                        'label' => 'product.fields.instagram_link',
                        'required' => false,
                    ])
                    ->add('slug', TextType::class, [
                        'label' => 'product.fields.slug',
                        'required' => false,
                        'attr' => ['readonly' => !$this->getSubject()->getId() ? false : true],
                    ])
                    ->add('productHasImage', CollectionType::class, [
                        'label' => 'product.fields.product_image',
                        'required' => false,
                        'constraints' => new Valid(),
                        'by_reference' => false,
                    ], [
                        'edit' => 'inline',
                        'inline' => 'table',
                        'sortable' => 'orderNum',
                        'link_parameters' => ['context' => $context],
                        'admin_code' => 'sonata.admin.product_has_image',
                    ])
                ->end()
                ->with('form_group.additional', ['class' => 'col-md-4', 'name' => null])
                    ->add('isActive', null, [
                        'label' => 'product.fields.is_active',
                        'required' => false,
                    ])
                    ->add('isAvailable', null, [
                        'label' => 'product.fields.is_available',
                        'required' => false,
                    ])
                    ->add('image', ModelListType::class, [
                        'label' => 'product.fields.image',
                        'required' => true,
                    ])
                    ->add('category', ModelListType::class, [
                        'label' => 'product.fields.category',
                        'required' => true,
                    ])
                    ->add('price', MoneyType::class, [
                        'label' => 'product.fields.price',
                        'required' => false,
                        'currency' => 'UAH',
                        'scale' => 0,
                    ])
                    ->add('discount', MoneyType::class, [
                        'label' => 'product.fields.discount',
                        'required' => false,
                        'currency' => 'UAH',
                        'scale' => 0,
                    ])
                    ->add('isWoman', null, [
                        'label' => 'product.fields.is_woman',
                        'required' => false,
                    ])
                    ->add('isMan', null, [
                        'label' => 'product.fields.is_man',
                        'required' => false,
                    ])
                    ->add('size', ModelListType::class, [
                        'label' => 'product.fields.size',
                        'required' => false,
                    ])
                    ->add('metals', ModelAutocompleteType::class, [
                        'label' => 'product.fields.metals',
                        'required' => false,
                        'property' => 'name',
                        'multiple' => true,
                        'attr' => ['class' => 'form-control'],
                        'callback' => function($admin, $property, $value) {
                            if (!$value) {
                                return;
                            }

                            $datagrid = $admin->getDatagrid();
                            $queryBuilder = $datagrid->getQuery();
                            $queryBuilder->andWhere($queryBuilder->getRootAlias() . '.isActive = 1');
                            $datagrid->setValue($property, null, $value);

                            return true;
                        },
                        'btn_catalogue' => $this->translationDomain,
                        'minimum_input_length' => 2,
                    ])
                    ->add('colours', ModelAutocompleteType::class, [
                        'label' => 'product.fields.colours',
                        'required' => false,
                        'property' => 'name',
                        'multiple' => true,
                        'attr' => ['class' => 'form-control'],
                        'callback' => function($admin, $property, $value) {
                            if (!$value) {
                                return;
                            }

                            $datagrid = $admin->getDatagrid();
                            $queryBuilder = $datagrid->getQuery();
                            $queryBuilder->andWhere($queryBuilder->getRootAlias() . '.isActive = 1');
                            $datagrid->setValue($property, null, $value);

                            return true;
                        },
                        'btn_catalogue' => $this->translationDomain,
                        'minimum_input_length' => 2,
                    ])
                    ->add('stones', ModelAutocompleteType::class, [
                        'label' => 'product.fields.stones',
                        'required' => false,
                        'property' => 'name',
                        'multiple' => true,
                        'attr' => ['class' => 'form-control'],
                        'callback' => function($admin, $property, $value) {
                            if (!$value) {
                                return;
                            }

                            $datagrid = $admin->getDatagrid();
                            $queryBuilder = $datagrid->getQuery();
                            $queryBuilder->andWhere($queryBuilder->getRootAlias() . '.isActive = 1');
                            $datagrid->setValue($property, null, $value);

                            return true;
                        },
                        'btn_catalogue' => $this->translationDomain,
                        'minimum_input_length' => 2,
                    ])
                    ->add('tags', ModelAutocompleteType::class, [
                        'label' => 'product.fields.tags',
                        'required' => false,
                        'property' => 'name',
                        'multiple' => true,
                        'attr' => ['class' => 'form-control'],
                        'callback' => function($admin, $property, $value) {
                            if (!$value) {
                                return;
                            }

                            $datagrid = $admin->getDatagrid();
                            $queryBuilder = $datagrid->getQuery();
                            $queryBuilder->andWhere($queryBuilder->getRootAlias() . '.isActive = 1');
                            $datagrid->setValue($property, null, $value);

                            return true;
                        },
                        'btn_catalogue' => $this->translationDomain,
                        'minimum_input_length' => 2,
                    ])
                    ->add('orderNum', IntegerType::class, [
                        'label' => 'product.fields.order_num',
                    ])
                    ->add('views', IntegerType::class, [
                        'label' => 'product.fields.views',
                        'required' => false,
                        'attr' => ['readonly' => true],
                    ])
                    ->add('productGroup', IntegerType::class, [
                        'label' => 'product.fields.product_group',
                        'required' => false,
                        'attr' => ['readonly' => true],
                    ])
                    ->add('updatedAt', DateTimePickerType::class, [
                        'label'     => 'product.fields.updated_at',
                        'required' => false,
                        'format' => 'dd-MM-YYYY HH:mm',
                        'attr' => ['readonly' => true],
                    ])
                ->end()
            ->end()
            ->with('product.tab.product_option_colour', ['tab' => true])
                ->with('form_group.product_option', ['class' => 'col-md-12', 'label' => null])
                    ->add('productHasOptionColour', CollectionType::class, [
                        'label' => 'product.fields.product_option_colour',
                        'required' => false,
                        'constraints' => new Valid(),
                        'by_reference' => false,
                    ], [
                        'edit' => 'inline',
                        'inline' => 'table',
                        'sortable' => 'orderNum',
                        'link_parameters' => ['context' => $context],
                        'admin_code' => 'sonata.admin.product_has_option_colour',
                    ])
                ->end()
            ->end()
            ->with('product.tab.product_option', ['tab' => true])
                ->with('form_group.product_option', ['class' => 'col-md-8', 'label' => null])
                    ->add('productHasOption', CollectionType::class, [
                        'label' => 'product.fields.product_option',
                        'required' => false,
                        'constraints' => new Valid(),
                        'by_reference' => false,
                    ], [
                        'edit' => 'inline',
                        'inline' => 'table',
                        'sortable' => 'orderNum',
                        'link_parameters' => ['context' => $context],
                        'admin_code' => 'sonata.admin.product_has_option',
                    ])
                ->end()
                ->with('form_group.product_option_2', ['class' => 'col-md-4', 'label' => null])
                    ->add('optionType', ChoiceType::class, [
                        'label' => 'product.fields.option_type',
                        'choices' => $this->getTypes(),
                        'required' => true,
                    ])
                ->end()
            ->end()
//            ->with('product.tab.product_option_metal', ['tab' => true])
//                ->with('form_group.product_option', ['class' => 'col-md-12', 'label' => null])
//                    ->add('productHasOptionMetal', CollectionType::class, [
//                        'label' => 'product.fields.product_option_metal',
//                        'required' => false,
//                        'constraints' => new Valid(),
//                        'by_reference' => false,
//                    ], [
//                        'edit' => 'inline',
//                        'inline' => 'table',
//                        'sortable' => 'orderNum',
//                        'link_parameters' => ['context' => $context],
//                        'admin_code' => 'sonata.admin.product_has_option_metal',
//                    ])
//                ->end()
//            ->end()
            ->with('product.tab.product_group', ['tab' => true])
                ->with('form_group.product_group', ['class' => 'col-md-12', 'name' => null])
                    ->add('productHasProduct', CollectionType::class, [
                        'label' => 'product.fields.product_set_products',
                        'required' => false,
                        'constraints' => new Valid(),
                        'by_reference' => false,
                    ], [
                        'edit' => 'inline',
                        'inline' => 'table',
                        'sortable' => 'orderNum',
                        'link_parameters' => ['context' => $context],
                        'admin_code' => 'sonata.admin.product_has_product',
                    ])
                ->end()
            ->end()
        ;
    }

    private function getTypes()
    {
        $matchEntity = $this->getClass();
        $typesEntity = $matchEntity::getTypes();

        $typesChoice = [];
        foreach ($typesEntity as $key => $value) {
            $typesChoice["product.fields.types.".$value] = $key;
        }

        return $typesChoice;
    }
}
