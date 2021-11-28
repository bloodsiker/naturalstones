<?php

namespace OrderBundle\Admin;

use AdminBundle\Admin\BaseAdmin as Admin;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use OrderBundle\Entity\OrderStatus;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Sonata\CoreBundle\Form\Type\CollectionType;
use Sonata\DoctrineORMAdminBundle\Filter\DateTimeFilter;
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
            ])
            ->add('phone', null, [
                'label' => 'order.fields.phone',
            ])
            ->add('email', null, [
                'label' => 'order.fields.email',
            ])
            ->add('status', 'choice', [
                'label' => 'order.fields.status',
                'choices' => array_flip($this->getStatuses()),
                'catalogue' => $this->getTranslationDomain(),
                'editable'  => true,
            ])
            ->add('createdAt', null, [
                'label' => 'order.fields.created_at',
            ])
            ->add('_action', 'actions', [
                'actions' => ['edit' => []],
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
            ->add('status', null, [
                'label' => 'order_board.fields.status',
            ], ChoiceType::class, [
                'choices' => $this->getStatuses(),
                'choice_translation_domain' => $this->getTranslationDomain(),
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('createdAt', DateTimeFilter::class, [
                'label' => 'order.fields.created_at',
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
            ->with('form_group.basic', ['class' => 'col-md-8', 'label' => false])
                ->add('fio', TextType::class, [
                    'label' => 'order.fields.fio',
                ])
                ->add('email', TextType::class, [
                    'label' => 'order.fields.email',
                ])
                ->add('phone', TextType::class, [
                    'label' => 'order.fields.phone',
                ])
                ->add('address', TextareaType::class, [
                    'label' => 'order.fields.address',
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
                ->add('createdAt', DateTimePickerType::class, [
                    'label'     => 'order.fields.created_at',
                    'required' => true,
                    'format' => 'YYYY-MM-dd HH:mm',
                    'attr' => ['readonly' => true],
                ])
            ->end()
            ->with('form_group.additional', ['class' => 'col-md-4', 'label' => false])
                ->add('status', ChoiceType::class, [
                    'label' => 'order.fields.status',
                    'choices' => $this->getStatuses(),
                    'required' => true,
                ])
                ->add('messenger', TextType::class, [
                    'label' => 'order.fields.messenger',
                    'required' => false,
                ])
                ->add('totalSum', TextType::class, [
                    'label' => 'order.fields.total_sum',
                    'attr' => ['readonly' => true],
                ])
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

        foreach ($statusesEntity as $key => $value) {
            $statusChoice["order.fields.statuses.".$value] = $key;
        }

        return $statusChoice;
    }
}
