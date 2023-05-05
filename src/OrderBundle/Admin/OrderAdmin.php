<?php

namespace OrderBundle\Admin;

use AdminBundle\Admin\BaseAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Sonata\CoreBundle\Form\Type\CollectionType;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * Class OrderAdmin
 */
class OrderAdmin extends Admin
{
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
     * {@inheritdoc}
     */
    public function setTranslationDomain($translationDomain)
    {
        $this->translationDomain = $translationDomain;
        $this->formOptions['translation_domain'] = $translationDomain;
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
     *
     * @throws \Exception
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, [
                'label' => 'order.fields.id',
            ])
            ->add('fio', null, [
                'label' => 'order.fields.fio',
                'template' => 'OrderBundle:Admin:list_fields.html.twig',
            ])
            ->add('orderSum', null, [
                'label' => 'order.fields.order_sum',
            ])
            ->add('type', 'choice', [
                'label' => 'order.fields.type',
                'choices' => array_flip($this->getTypes()),
                'catalogue' => $this->getTranslationDomain(),
            ])
            ->add('status', 'choice', [
                'label' => 'order.fields.status',
                'choices' => array_flip($this->getStatuses()),
                'catalogue' => $this->getTranslationDomain(),
                'editable'  => true,
            ])
            ->add('isSpin', null, [
                'label' => 'order.fields.is_spin',
            ])
            ->add('createdAt', null, [
                'label' => 'order.fields.created_at',
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'preview' => ['template' => 'OrderBundle:CRUD:list__action_preview.html.twig'],
                    'edit' => []
                ],
            ]);
    }

    /**
     * @param DatagridMapper $datagridMapper
     *
     * @throws \Exception
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('email', null, [
                'label' => 'order.fields.email',
            ])
            ->add('fio', null, [
                'label' => 'order.fields.fio',
            ])
            ->add('phone', null, [
                'label' => 'order.fields.phone',
            ])
            ->add('instagram', null, [
                'label' => 'order.fields.instagram',
            ])
            ->add('type', null, [
                'label' => 'order.fields.type',
            ], ChoiceType::class, [
                'choices' => $this->getTypes(),
                'choice_translation_domain' => $this->getTranslationDomain(),
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('status', null, [
                'label' => 'order.fields.status',
            ], ChoiceType::class, [
                'choices' => $this->getStatuses(),
                'choice_translation_domain' => $this->getTranslationDomain(),
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('createdAt', DateFilter::class, [
                'label'         => 'order.fields.created_at',
                'field_type'    => DateTimePickerType::class,
                'field_options' => array('format' => 'dd.MM.yyyy'),
                'show_filter' => true,
            ]);
    }

    /**
     * @param FormMapper $formMapper
     *
     * @throws \Exception
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $context = $this->getPersistentParameter('context');

        $formMapper
            ->with('order.tab.order', ['tab' => true])
                ->with('form_group.basic', ['class' => 'col-md-8', 'label' => false])
                    ->add('fio', TextType::class, [
                        'label' => 'order.fields.fio',
                        'required' => false,
                    ])
                    ->add('email', TextType::class, [
                        'label' => 'order.fields.email',
                        'required' => false,
                    ])
                    ->add('phone', TextType::class, [
                        'label' => 'order.fields.phone',
                        'required' => false,
                    ])
                    ->add('address', TextareaType::class, [
                        'label' => 'order.fields.address',
                        'required' => false,
                        'attr' => [
                            'rows' => 3,
                        ],
                    ])
                    ->add('comment', TextareaType::class, [
                        'label' => 'order.fields.comment',
                        'required' => false,
                        'attr' => [
                            'rows' => 3,
                        ],
                    ])
                    ->add('createdAt', DateTimePickerType::class, [
                        'label'     => 'order.fields.created_at',
                        'required' => true,
                        'format' => 'YYYY-MM-dd HH:mm',
                        'attr' => ['readonly' => true],
                    ])
                ->end()
                ->with('form_group.additional', ['class' => 'col-md-4', 'label' => false])
                    ->add('callMe', null, [
                        'label' => 'order.fields.call_me',
                        'required' => false,
                    ])
                    ->add('status', ChoiceType::class, [
                        'label' => 'order.fields.status',
                        'choices' => $this->getStatuses(),
                        'required' => true,
                    ])
                    ->add('type', ChoiceType::class, [
                        'label' => 'order.fields.type',
                        'choices' => $this->getTypes(),
                        'required' => true,
                    ])
                    ->add('messenger', TextType::class, [
                        'label' => 'order.fields.messenger',
                        'required' => false,
                    ])
                    ->add('instagram', TextType::class, [
                        'label' => 'order.fields.instagram',
                        'required' => false,
                    ])
                    ->add('totalSum', TextType::class, [
                        'label' => 'order.fields.total_sum',
                        'attr' => ['readonly' => true],
                    ])
                    ->add('discountPromo', TextType::class, [
                        'label' => 'order.fields.discount_promo',
                        'attr' => ['readonly' => true],
                    ])
                    ->add('orderSum', TextType::class, [
                        'label' => 'order.fields.order_sum',
                        'attr' => ['readonly' => true],
                    ])
                    ->add('isSpin', null, [
                        'label' => 'order.fields.is_spin',
                        'required' => false,
                    ])
                    ->add('wheelSpinOption', ModelListType::class, [
                        'label' => 'order.fields.wheel_spin_option',
                        'btn_edit' => 'btn_edit',
                        'required' => false,
                    ])
                    ->add('spinPrize', TextareaType::class, [
                        'label' => 'order.fields.spin_prize',
                        'required' => false,
                        'attr' => [
                            'rows' => 3,
                        ],
                    ])
                ->end()
            ->end()
            ->with('order.tab.order_has_product', ['tab' => true])
                ->with('form_group.order_has_product', ['class' => 'col-md-12', 'label' => null])
                    ->add('orderHasItems', CollectionType::class, [
                        'label' => 'order.fields.items',
                        'required' => false,
                        'constraints' => new Valid(),
                        'by_reference' => false,
                    ], [
                        'edit' => 'inline',
                        'inline' => 'table',
                        'sortable' => 'orderNum',
                        'link_parameters' => ['context' => $context],
                        'admin_code' => 'order.admin.order_has_item',
                    ])
                ->end()
            ->end();
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    private function getStatuses()
    {
        $matchEntity = $this->getClass();
        $statusesEntity = $matchEntity::getStatuses();
        $statusChoice = [];

        foreach ($statusesEntity as $key => $value) {
            $statusChoice["order.fields.statuses.".$value] = $key;
        }

        return $statusChoice;
    }

    private function getTypes()
    {
        $matchEntity = $this->getClass();
        $statusesEntity = $matchEntity::getTypes();
        $statusChoice = [];

        foreach ($statusesEntity as $key => $value) {
            $statusChoice["order.fields.types.".$value] = $key;
        }

        return $statusChoice;
    }
}
