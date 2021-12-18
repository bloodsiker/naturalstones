<?php

namespace ProductBundle\Admin;

use AdminBundle\Admin\BaseAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class ProductSearchHistoryAdmin
 */
class ProductSearchHistoryAdmin extends Admin
{
    protected $datagridValues = [
        '_page'       => 1,
        '_per_page'   => 25,
        '_sort_by'    => 'id',
        '_sort_order' => 'DESC',
    ];

    /**
     * ProductSearchHistory constructor.
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

        $this->datagridValues['createdAt'] = [
            'value' => (new \DateTime('now'))->format('d.m.Y'),
        ];
    }

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
                'label' => 'history_search.fields.id',
            ])
            ->addIdentifier('search', null, [
                'label' => 'history_search.fields.search',
            ])
            ->add('ip', null, [
                'label' => 'history_search.fields.ip',
            ])
            ->add('createdAt', null, [
                'label' => 'history_search.fields.created_at',
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
            ->add('search', null, [
                'label' => 'history_search.fields.search',
            ])
            ->add('ip', null, [
                'label' => 'history_search.fields.ip',
            ])
            ->add('createdAt', DateFilter::class, [
                'label'         => 'history_search.fields.created_at',
                'field_type'    => DateTimePickerType::class,
                'field_options' => array('format' => 'dd.MM.yyyy'),
                'show_filter'   => true,
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form_group.basic', ['class' => 'col-md-8', 'name' => false])
                ->add('search', TextType::class, [
                    'label' => 'history_search.fields.search',
                ])
            ->end()
            ->with('form_group.additional', ['class' => 'col-md-4', 'name' => false])
                ->add('ip', TextType::class, [
                    'label' => 'history_search.fields.ip',
                    'required' => false,
                    'attr' => ['readonly' => !$this->getSubject()->getId() ? false : true],
                ])
                ->add('createdAt', DateTimePickerType::class, [
                    'label'     => 'history_search.fields.created_at',
                    'required' => true,
                    'format' => 'YYYY-MM-dd HH:mm',
                    'attr' => ['readonly' => true],
                ])
            ->end();
    }
}
