<?php

namespace ProductBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * Class ProductHasImageAdmin
 */
class ProductHasImageAdmin extends Admin
{
    protected $parentAssociationMapping = 'product';

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('product', null, [
                'label' => 'product_has_image.fields.product',
            ])
            ->add('image', null, [
                'label' => 'product_has_image.fields.image',
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
            ->add('image', ModelListType::class, [
                'label' => 'product_has_image.fields.image',
                'required'      => true,
                'btn_delete'    => false,
            ], ['link_parameters' => $linkParameters])
            ->add('orderNum', HiddenType::class, [
                'label' => 'product_has_image.fields.order_num',
            ])
        ;
    }
}
