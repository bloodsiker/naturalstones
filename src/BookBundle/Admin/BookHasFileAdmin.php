<?php

namespace BookBundle\Admin;

use MediaBundle\Twig\Extension\MediaExtension;
use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class BookHasFileAdmin
 */
class BookHasFileAdmin extends Admin
{
    protected $parentAssociationMapping = 'book';

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('book', null, [
                'label' => 'book_has_file.fields.book',
            ])
            ->add('bookFile', null, [
                'label' => 'book_has_file.fields.file',
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
            ->add('bookFile', ModelListType::class, [
                'label' => 'book_has_file.fields.file',
                'required' => true,
            ], ['link_parameters' => $linkParameters])
        ;
        if ($this->getSubject() && $this->getSubject()->getId()) {
            $extensionFile = MediaExtension::getExtensionFile($this->getSubject()->getBookFile()->getPath());
            $formMapper
                ->add('bookFile.mimeType', TextType::class, [
                    'label' => 'book_has_file.fields.type',
                    'required' => false,
                    'empty_data' => $extensionFile,
                    'attr' => [
                        'readonly' => true,
                        'disabled' => true,
                        'value' => $extensionFile,
                    ],
                ])
            ;
        }
        $formMapper
            ->add('orderNum', HiddenType::class, [
                'label' => 'book_has_file.fields.order_num',
            ]);
    }
}
