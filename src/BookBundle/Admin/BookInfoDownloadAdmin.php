<?php

namespace BookBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class BookInfoDownloadAdmin
 */
class BookInfoDownloadAdmin extends Admin
{
    /**
     * @var array $datagridValues
     */
    protected $datagridValues = [
        '_page'       => 1,
        '_per_page'   => 25,
        '_sort_by'    => 'id',
        '_sort_order' => 'DESC',
    ];

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', null, [
                'label' => 'book_download.fields.id',
            ])
            ->add('book', null, [
                'label' => 'book_download.fields.book',
            ])
            ->add('downloadAt', null, [
                'label' => 'book_download.fields.download_at',
            ])
            ->add('ip', null, [
                'label' => 'book_download.fields.ip',
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'delete' => [],
                ],
            ])
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('book', null, [
                'label' => 'book_download.fields.book',
            ])
            ->add('ip', null, [
                'label' => 'book_download.fields.ip',
            ])
            ->add('downloadAt', DateFilter::class, [
                'label'         => 'book_download.fields.download_at',
                'field_type'    => DateTimePickerType::class,
                'field_options' => array('format' => 'dd.MM.yyyy'),
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form_group.basic', ['class' => 'col-md-8', 'name' => null])
                ->add('ip', TextType::class, [
                    'label' => 'book_download.fields.ip',
                    'required' => false,
                ])
                ->add('downloadAt', DateTimePickerType::class, [
                    'label'     => 'book_download.fields.download_at',
                    'required' => false,
                    'format' => 'dd-MM-YYYY HH:mm',
                    'attr' => ['readonly' => true],
                ])
            ->end()
            ->with('form_group.additional', ['class' => 'col-md-4', 'name' => null])
                ->add('book', ModelListType::class, [
                    'label' => 'book_download.fields.book',
                    'required' => false,
                ])
            ->end()
        ;
    }
}
