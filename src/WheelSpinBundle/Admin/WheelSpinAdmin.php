<?php

namespace WheelSpinBundle\Admin;

use AdminBundle\Admin\BaseAdmin as Admin;
use AppBundle\Traits\FixAdminFormTranslationDomainTrait;
use Doctrine\ORM\EntityManager;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * Class WheelSpinAdmin
 */
class WheelSpinAdmin extends Admin
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
        '_sort_by'    => 'orderNum',
        '_sort_order' => 'ASC',
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
        $totalValuation = 0;
        foreach ($object->getWheelSpinHasOption()->getValues() as $option) {
            $totalValuation += $option->getValuation();
        }

        foreach ($object->getWheelSpinHasOption()->getValues() as $option) {
            $option->setPercent(round($option->getValuation() * 100 / $totalValuation, 2));
        }

        $this->entityManager->persist($object);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate($object)
    {
        $totalValuation = 0;
        foreach ($object->getWheelSpinHasOption()->getValues() as $option) {
            $totalValuation += $option->getValuation();
        }

        foreach ($object->getWheelSpinHasOption()->getValues() as $option) {
            $option->setPercent(round($option->getValuation() * 100 / $totalValuation, 2));
        }

        $this->entityManager->persist($object);
        $this->entityManager->flush();
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', null, [
                'label' => 'wheel_spin.fields.id',
            ])
            ->add('minSum', null, [
                'label' => 'wheel_spin.fields.min_sum',
            ])
            ->add('maxSum', null, [
                'label' => 'wheel_spin.fields.max_sum',
            ])
            ->add('isActive', null, [
                'label' => 'wheel_spin.fields.is_active',
                'editable'  => true,
            ])
            ->add('_action', 'actions', [
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
            ->add('isActive', null, [
                'label' => 'wheel_spin.fields.is_active',
            ])
            ->add('minSum', null, [
                'label' => 'wheel_spin.fields.min_sum',
            ])
            ->add('maxSum', null, [
                'label' => 'wheel_spin.fields.max_sum',
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $context = $this->getPersistentParameter('context');

        $formMapper
            ->with('form_group.basic', ['class' => 'col-md-12', 'name' => false])
                ->add('isActive', null, [
                    'label' => 'wheel_spin.fields.is_active',
                    'required' => false,
                ])
                ->add('minSum', TextType::class, [
                    'label' => 'wheel_spin.fields.min_sum',
                    'required' => true,
                ])
                ->add('maxSum', TextType::class, [
                    'label' => 'wheel_spin.fields.max_sum',
                    'required' => true,
                ])
            ->end()
            ->with('form_group.additional', ['class' => 'col-md-12', 'name' => false])
                ->add('wheelSpinHasOption', CollectionType::class, [
                    'label' => 'wheel_spin.fields.prizes',
                    'required' => false,
                    'constraints' => new Valid(),
                    'by_reference' => false,
                ], [
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable' => 'orderNum',
                    'link_parameters' => ['context' => $context],
                    'admin_code' => 'wheel_spin.admin.wheel_spin_has_option',
                ])
            ->end();
    }
}
