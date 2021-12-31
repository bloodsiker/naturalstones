<?php

namespace ProductBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class ProductHasProductAdmin
 */
class ProductHasProductAdmin extends Admin
{
    protected $parentAssociationMapping = 'product';

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('product', null, [
                'label' => 'product_has_product.fields.product',
            ])
            ->add('setProduct', null, [
                'label' => 'product_has_product.fields.set_product',
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

        if ($this->getSubject() && $this->getSubject()->getId()) {
            $object = $this->getSubject();
        } else {
            $object = null;
        }

        $formMapper
            ->add('productSet', ModelListType::class, [
                'label' => 'product_has_product.fields.product_set',
                'required'      => true,
                'btn_delete'    => false,
            ], ['link_parameters' => $linkParameters])
            ->add('quantity', IntegerType::class, [
                'label' => 'product_has_product.fields.quantity',
                'required' => false,
            ])
            ->add('price', TextType::class, [
                'label' => 'product_has_product.fields.price',
                'required' => false,
                'attr' => [
                    'readonly' => true,
                    'value' => $object ? $object->getProductSet()->getPrice() : null,
                ],
            ])
            ->add('discount', TextType::class, [
                'label' => 'product_has_product.fields.discount',
                'required' => false,
                'attr' => [
                    'readonly' => true,
                    'value' => $object ? $object->getProductSet()->getDiscount() : null,
                ],
            ])
            ->add('size', TextType::class, [
                'label' => 'product_has_product.fields.size',
                'required' => false,
                'attr' => [
                    'readonly' => true,
                    'value' => $object ? $object->getProductSet()->getSize() : null,
                ],
            ])
            ->add('orderNum', HiddenType::class, [
                'label' => 'product_has_product.fields.order_num',
            ])
        ;
    }
}
