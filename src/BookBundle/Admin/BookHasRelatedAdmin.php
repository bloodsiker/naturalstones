<?php

namespace BookBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * Class BookHasRelatedAdmin
 */
class BookHasRelatedAdmin extends Admin
{
    protected $parentAssociationMapping = 'book';

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('book', null, [
                'label' => 'book_has_related.fields.book',
            ])
            ->add('relatedBook', null, [
                'label' => 'book_has_related.fields.book_related',
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
            ->add('relatedBook', ModelListType::class, [
                'label' => 'book_has_related.fields.book_related',
                'required'      => true,
                'btn_add'       => false,
                'btn_edit'      => false,
                'btn_delete'    => false,
            ], ['link_parameters' => $linkParameters])
            ->add('orderNum', HiddenType::class, [
                'label' => 'book_has_related.fields.order_num',
            ])
        ;
    }
}
