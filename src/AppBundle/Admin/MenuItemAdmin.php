<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class MenuItemAdmin extends Admin
{
    protected $parentAssociationMapping = 'menuSection';

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('category', null, ['label' => 'menu_item.fields.category'])
            ->add('_action', 'actions', [
                'actions' => ['delete' => []],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('category', ModelListType::class, [
                'label' => 'menu_item.fields.category',
                'required' => true,
                'btn_delete' => false,
            ])
            ->add('orderNum', HiddenType::class, [
                'label' => 'menu_item.fields.order_num',
            ]);
    }
}
