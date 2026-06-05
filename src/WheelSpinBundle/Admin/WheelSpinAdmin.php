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
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var array
     */
    protected $datagridValues = [
        '_page' => 1,
        '_per_page' => 25,
        '_sort_by' => 'orderNum',
        '_sort_order' => 'ASC',
    ];

    /**
     * @return EntityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        return $this->entityManager = $entityManager;
    }

    public function prePersist(object $object): void
    {
        $this->recalculateOptions($object);
    }

    public function preUpdate(object $object): void
    {
        $this->recalculateOptions($object);
    }

    private function recalculateOptions(object $object): void
    {
        $options = $object->getWheelSpinHasOption()->getValues();

        usort($options, static fn ($a, $b) => $a->getOrderNum() <=> $b->getOrderNum());

        $maxValuation = (int) max(array_map(static fn ($o) => (int) $o->getValuation(), $options) ?: [0]);
        $weights = [];

        foreach ($options as $option) {
            $weights[] = max(1, $maxValuation - (int) $option->getValuation() + 1);
        }

        $totalWeight = array_sum($weights);

        foreach ($options as $k => $option) {
            $option->setDegrees($options ? self::computeDegrees($k, count($options)) : '');
            $option->setPercent(
                $totalWeight > 0 ? round(($weights[$k] ?? 0) * 100 / $totalWeight, 2) : 0.0
            );
        }
    }

    private static function computeDegrees(int $k, int $N): string
    {
        $step = 360.0 / $N;
        $hi = (int) round(90 - $k * $step);
        $lo = (int) round(90 - ($k + 1) * $step);

        if ($lo === 0) {
            return sprintf('1:%d', $hi);
        }

        while ($hi <= 0) {
            $hi += 360;
        }
        while ($lo <= 0) {
            $lo += 360;
        }
        if ($hi >= 360) {
            $hi = 359;
        }

        if ($lo < $hi) {
            return sprintf('%d:%d', $lo, $hi);
        }

        return sprintf('1:%d,%d:359', $hi, $lo);
    }

    protected function configureListFields(ListMapper $listMapper): void
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
                'editable' => true,
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => [],
                ],
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
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

    protected function configureFormFields(FormMapper $formMapper): void
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
