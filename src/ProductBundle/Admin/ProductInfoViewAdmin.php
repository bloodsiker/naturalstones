<?php

namespace ProductBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class ProductInfoViewAdmin
 */
class ProductInfoViewAdmin extends Admin
{
    /**
     * @var array $datagridValues
     */
    protected $datagridValues = [
        '_page'       => 1,
        '_per_page'   => 64,
        '_sort_by'    => 'id',
        '_sort_order' => 'DESC',
    ];

    /**
     * ArticleAdmin constructor.
     *
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     *
     * @throws \Exception
     */
    public function __construct($code, $class, $baseControllerName)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->datagridValues['viewAt'] = [
            'value' => (new \DateTime('now'))->format('d.m.Y'),
        ];
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', null, [
                'label' => 'product_view.fields.id',
            ])
            ->add('product', null, [
                'label' => 'product_view.fields.product',
                'template'  => 'ProductBundle:Admin:list_fields.html.twig',
            ])
            ->add('views', null, [
                'label' => 'product_view.fields.views',
            ])
            ->add('viewAt', null, [
                'label' => 'product_view.fields.view_at',
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'delete' => [],
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
            ->add('product', null, [
                'label' => 'product_view.fields.product',
            ])
            ->add('viewAt', DateFilter::class, [
                'label'         => 'product_view.fields.view_at',
                'field_type'    => DateTimePickerType::class,
                'field_options' => array('format' => 'dd.MM.yyyy'),
                'show_filter' => true,
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form_group.basic', ['class' => 'col-md-8', 'name' => null])
                ->add('views', IntegerType::class, [
                    'label' => 'product_view.fields.views',
                    'required' => false,
                ])
                ->add('viewAt', DateTimePickerType::class, [
                    'label'     => 'product_view.fields.view_at',
                    'required' => false,
                    'format' => 'dd-MM-YYYY HH:mm',
                    'attr' => ['readonly' => true],
                ])
            ->end()
            ->with('form_group.additional', ['class' => 'col-md-4', 'name' => null])
                ->add('product', ModelListType::class, [
                    'label' => 'product_view.fields.product',
                    'required' => false,
                ])
            ->end()
        ;
    }
}
