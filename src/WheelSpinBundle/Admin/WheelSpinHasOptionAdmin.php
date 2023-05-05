<?php

namespace WheelSpinBundle\Admin;

use AdminBundle\Form\Type\ColorPickerType;
use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class WheelSpinHasOptionAdmin
 */
class WheelSpinHasOptionAdmin extends Admin
{
    protected $parentAssociationMapping = 'wheelSpin';

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('wheelSpinOption', null, [
                'label' => 'wheel_spin_has_option.fields.option',
            ])
            ->add('label', null, [
                'label' => 'wheel_spin_has_option.fields.label',
            ])
            ->add('colour', null, [
                'label' => 'wheel_spin_has_option.fields.colour',
            ])
            ->add('degrees', null, [
                'label' => 'wheel_spin_has_option.fields.degrees',
            ])
            ->add('percent', null, [
                'label' => 'wheel_spin_has_option.fields.percent',
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'delete' => [],
                ],
            ])
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $linkParameters = [];

        if ($this->hasParentFieldDescription()) {
            $linkParameters = $this->getParentFieldDescription()->getOption('link_parameters', []);
        }

        if ($this->hasRequest()) {
            $context = $this->getRequest()->get('context', null);

            if (null !== $context) {
                $linkParameters['context'] = $context;
            }
        }

        $formMapper
            ->add('wheelSpinOption', ModelListType::class, [
                'label' => 'wheel_spin_has_option.fields.option',
                'required'      => true,
                'btn_delete'    => false,
                'btn_edit'      => 'btn_edit',
            ], ['link_parameters' => $linkParameters])
            ->add('label', TextType::class, [
                'label' => 'wheel_spin_has_option.fields.label',
                'required' => true,
            ])
            ->add('colour', ColorPickerType::class, [
                'label' => 'wheel_spin_has_option.fields.colour',
                'required' => false,
            ])
            ->add('degrees', TextType::class, [
                'label' => 'wheel_spin_has_option.fields.degrees',
                'required' => true,
            ])
            ->add('valuation', IntegerType::class, [
                'label' => 'wheel_spin_has_option.fields.valuation',
                'required' => false,
                'scale' => 0,
            ])
            ->add('percent', TextType::class, [
                'label' => 'wheel_spin_has_option.fields.percent',
                'required' => false,
                'attr' => [
                    'readonly' => true,
                    'disabled' => true,
                ],
            ])
            ->add('orderNum', HiddenType::class, [
                'label' => 'wheel_spin_has_option.fields.order_num',
            ])
        ;
    }
}
