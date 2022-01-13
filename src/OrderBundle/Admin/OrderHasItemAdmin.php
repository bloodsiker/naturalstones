<?php

namespace OrderBundle\Admin;

use AdminBundle\Form\Type\TextareaCounterType;
use AdminBundle\Form\Type\TextCounterType;
use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

/**
 * Class OrderHasItemAdmin
 */
class OrderHasItemAdmin extends Admin
{
    protected $parentAssociationMapping = 'product';

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('order', null, [
                'label' => 'order_has_item.fields.order',
            ])
            ->add('product', null, [
                'label' => 'order_has_item.fields.product',
            ])
            ->add('colour', null, [
                'label' => 'order_has_item.fields.colour',
            ])
            ->add('quantity', null, [
                'label' => 'order_has_item.fields.quantity',
            ])
            ->add('price', null, [
                'label' => 'order_has_item.fields.price',
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
            ->add('product', ModelListType::class, [
                'label' => 'order_has_item.fields.product',
                'required' => true,
                'btn_delete' => false,
                'btn_add' => false,
                'btn_edit' => false,
            ], ['link_parameters' => $linkParameters])
            ->add('colour', ModelListType::class, [
                'label' => 'order_has_item.fields.colour',
                'required' => true,
                'btn_delete' => false,
                'btn_add' => false,
                'btn_edit' => false,
            ], ['link_parameters' => $linkParameters])
            ->add('quantity', IntegerType::class, [
                'label' => 'order_has_item.fields.quantity',
                'required' => false,
            ])
            ->add('price', MoneyType::class, [
                'label' => 'order_has_item.fields.price',
                'currency' => 'uah',
                'required' => false,
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('discount', MoneyType::class, [
                'label' => 'order_has_item.fields.discount',
                'currency' => 'uah',
                'required' => false,
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('options', TextareaCounterType::class, [
                'label' => 'order_has_item.fields.option',
            ])
        ;
    }
}
