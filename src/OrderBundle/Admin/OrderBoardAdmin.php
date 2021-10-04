<?php

namespace OrderBundle\Admin;

use AdminBundle\Admin\BaseAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Sonata\DoctrineORMAdminBundle\Filter\DateTimeFilter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class OrderBoardAdmin
 */
class OrderBoardAdmin extends Admin
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
                'label' => 'order_board.fields.id',
            ])
            ->addIdentifier('bookTitle', null, [
                'label' => 'order_board.fields.book_title',
            ])
            ->add('status', 'choice', [
                'label' => 'order_board.fields.status',
                'choices' => array_flip($this->getStatuses()),
                'catalogue' => $this->getTranslationDomain(),
                'editable'  => true,
            ])
            ->add('vote', null, [
                'label' => 'order_board.fields.vote',
            ])
            ->add('user', null, [
                'label' => 'order_board.fields.user',
            ])
            ->add('userName', null, [
                'label' => 'order_board.fields.user_name',
            ])
            ->add('createdAt', null, [
                'label' => 'order_board.fields.created_at',
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => [],
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
            ->add('bookTitle', null, [
                'label' => 'order_board.fields.book_title',
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
                'label' => 'order_board.fields.created_at',
            ]);
    }

    /**
     * @param FormMapper $formMapper
     *
     * @throws \Exception
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form_group.basic', ['class' => 'col-md-8', 'name' => false])
                ->add('bookTitle', TextType::class, [
                    'label' => 'order_board.fields.book_title',
                ])
                ->add('userName', TextType::class, [
                    'label' => 'order_board.fields.user_name',
                    'required' => false,
                ])
            ->end()
            ->with('form_group.additional', ['class' => 'col-md-4', 'name' => false])
                ->add('user', ModelListType::class, [
                    'label' => 'order_board.fields.user',
                    'required' => false,
                ])
                ->add('status', ChoiceType::class, [
                    'label' => 'order_board.fields.status',
                    'choices' => $this->getStatuses(),
                    'required' => true,
                ])
                ->add('book', ModelListType::class, [
                    'label' => 'order_board.fields.book',
                    'btn_add' => false,
                    'btn_edit' => false,
                    'required' => false,
                ])
                ->add('vote', IntegerType::class, [
                    'label' => 'order_board.fields.vote',
                    'required' => false,
                ])
                ->add('createdAt', DateTimePickerType::class, [
                    'label'     => 'order_board.fields.created_at',
                    'required' => true,
                    'format' => 'YYYY-MM-dd HH:mm',
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
            $statusChoice["order_board.fields.statuses.".$value] = $key;
        }

        return $statusChoice;
    }
}
