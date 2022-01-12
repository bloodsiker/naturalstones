<?php

namespace ProductBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class ProductHasOptionAdmin
 */
class ProductHasOptionAdmin extends Admin
{
    protected $parentAssociationMapping = 'product';

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('product', null, [
                'label' => 'product_has_option.fields.product',
            ])
            ->add('image', null, [
                'label' => 'product_has_option.fields.image',
            ])
            ->add('value', null, [
                'label' => 'product_has_option.fields.value',
            ])
            ->add('price', null, [
                'label' => 'product_has_option.fields.price',
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
//            ->add('image', ModelListType::class, [
//                'label' => 'product_has_option.fields.image',
//                'required'      => false,
//                'btn_delete'    => false,
//            ], ['link_parameters' => $linkParameters])
            ->add('value', TextType::class, [
                'label' => 'product_has_option.fields.value',
                'required' => false,
            ])
//            ->add('price', TextType::class, [
//                'label' => 'product_has_option.fields.price',
//                'required' => false,
//            ])
            ->add('orderNum', HiddenType::class, [
                'label' => 'product_has_option.fields.order_num',
            ])
        ;
    }
}
