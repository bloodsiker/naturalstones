<?php

namespace ProductBundle\Admin;

use AdminBundle\Admin\BaseAdmin as Admin;
use AdminBundle\Form\Type\TextCounterType;
use Doctrine\ORM\EntityManager;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * Class ProductAdmin
 */
class ProductAdmin extends Admin
{

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
        ;
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('acl');

        $collection->add('preview', 'preview');
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
                'template'  => 'ProductBundle:Admin:list_fields.html.twig',
            ])
            ->add('category', null, [
                'label'     => 'product.fields.category',
            ])
            ->add('colours', null, [
                'label' => 'product.fields.colours',
                'template'  => 'ProductBundle:Admin:list_fields.html.twig',
            ])
            ->add('sizes', null, [
                'label' => 'product.fields.sizes',
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
            ->add('createdAt', null, [
                'label' => 'product.fields.created_at',
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'preview' => ['template' => 'ProductBundle:CRUD:list__action_preview.html.twig'],
                    'edit' => [],
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
            ->add('colours', null, [
                'label' => 'product.fields.colours',
            ])
            ->add('price', null, [
                'label' => 'product.fields.price',
            ])
            ->add('tags', null, [
                'label' => 'product.fields.tags',
            ])
            ->add('sizes', null, [
                'label' => 'product.fields.sizes',
            ])
            ->add('isActive', null, [
                'label' => 'product.fields.is_active',
            ])
            ->add('isAvailable', null, [
                'label' => 'product.fields.is_available',
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
        $formMapper
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
                ->add('colours', ModelAutocompleteType::class, [
                    'label' => 'product.fields.colours',
                    'required' => false,
                    'property' => 'name',
                    'multiple' => true,
                    'attr' => ['class' => 'form-control'],
                    'btn_catalogue' => $this->translationDomain,
                    'minimum_input_length' => 2,
                ])
                ->add('stones', ModelAutocompleteType::class, [
                    'label' => 'product.fields.stones',
                    'required' => false,
                    'property' => 'name',
                    'multiple' => true,
                    'attr' => ['class' => 'form-control'],
                    'btn_catalogue' => $this->translationDomain,
                    'minimum_input_length' => 2,
                ])
                ->add('sizes', ModelAutocompleteType::class, [
                    'label' => 'product.fields.sizes',
                    'required' => false,
                    'property' => 'name',
                    'multiple' => true,
                    'attr' => ['class' => 'form-control'],
                    'btn_catalogue' => $this->translationDomain,
                    'minimum_input_length' => 2,
                ])
                ->add('tags', ModelAutocompleteType::class, [
                    'label' => 'product.fields.tags',
                    'required' => false,
                    'property' => 'name',
                    'multiple' => true,
                    'attr' => ['class' => 'form-control'],
                    'btn_catalogue' => $this->translationDomain,
                    'minimum_input_length' => 2,
                ])
                ->add('views', IntegerType::class, [
                    'label' => 'product.fields.views',
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
        ;
    }
}
