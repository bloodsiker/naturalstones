<?php

namespace ProductBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class ProductHasVideoAdmin
 */
class ProductHasVideoAdmin extends Admin
{
    protected $parentAssociationMapping = 'product';

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('product', null, [
                'label' => 'product_has_video.fields.product',
            ])
            ->add('image', null, [
                'label' => 'product_has_video.fields.video',
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
            ->add('video', ModelListType::class, [
                'label' => 'product_has_video.fields.video',
                'required'      => true,
                'btn_delete'    => false,
            ], ['link_parameters' => $linkParameters])
            ->add('path', TextType::class, [
                'label' => 'product_has_video.fields.path',
                'required' => false,
                'attr' => [
                    'readonly' => true,
                    'value' => $object ? $object->getVideo()->getPath() : null,
                ],
            ])
            ->add('orderNum', HiddenType::class, [
                'label' => 'product_has_video.fields.order_num',
            ])
        ;
    }
}
