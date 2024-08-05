<?php

namespace ShareBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class StoneHasConstructorAdmin
 */
class StoneHasConstructorAdmin extends Admin
{
    protected $parentAssociationMapping = 'stone';

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('stone', null, [
                'label' => 'stone_has_constructor.fields.stone',
            ])
            ->add('image', null, [
                'label' => 'stone_has_constructor.fields.image',
            ])
            ->add('size', null, [
                'label' => 'stone_has_constructor.fields.size',
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
                'label' => 'stone_has_constructor.fields.image',
                'required'      => false,
                'btn_delete'    => false,
            ], ['link_parameters' => $linkParameters])
            ->add('size', TextType::class, [
                'label' => 'stone_has_constructor.fields.size',
                'required' => false,
            ])
            ->add('orderNum', HiddenType::class, [
                'label' => 'stone_has_constructor.fields.order_num',
            ])
        ;
    }
}
