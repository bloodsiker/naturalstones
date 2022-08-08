<?php

namespace ShareBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use AdminBundle\Admin\BaseAdmin as Admin;
use AppBundle\Traits\FixAdminFormTranslationDomainTrait;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class TextAdmin
 */
class TextAdmin extends Admin
{
    use FixAdminFormTranslationDomainTrait;

    protected $datagridValues = [
        '_page'       => 1,
        '_per_page'   => 25,
        '_sort_by'    => 'id',
        '_sort_order' => 'DESC',
    ];

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('acl');

        $collection->add('preview', 'preview');
    }

    /**
     * @return array
     */
    public function getPersistentParameters()
    {
        if (!$this->hasRequest()) {
            return [];
        }

        $parameters = array_filter($this->getRequest()->query->all(), function ($param) {
            return !is_array($param);
        });

        return $parameters;
    }

    /**
     * @param string $name
     *
     * @return null|string|void
     */
    public function getTemplate($name)
    {
        $parameters = $this->getPersistentParameters();
        if (in_array($name, ['list', 'edit']) && !empty($parameters['CKEditor'])) {
            return 'AdminBundle:Ckeditor:ajax.html.twig';
        }

        return parent::getTemplate($name);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, [
                'label' => 'text.fields.id',
            ])
            ->addIdentifier('name', null, [
                'label' => 'text.fields.name',
            ])
            ->add('isActive', null, [
                'label' => 'text.fields.is_active',
                'editable'  => true,
            ])
            ->add('_action', 'actions', [
                'template' => isset($this->getPersistentParameters()['CKEditor']) ? 'AdminBundle:Ckeditor:select.html.twig' : null,
                'actions' => [
                    'edit' => [],
                ],
            ]);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('translations.name', null, [
                'label' => 'text.fields.name',
            ])
            ->add('isActive', null, [
                'label' => 'text.fields.is_active',
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form_group.basic', ['class' => 'col-md-8', 'name' => false])
                ->add('translations', TranslationsType::class, [
                    'translation_domain' => $this->translationDomain,
                    'label' => false,
                    'fields' => [
                        'name' => [
                            'label' => 'text.fields.name',
                            'field_type' => TextType::class,
                            'required' => true,
                        ],
                        'description' => [
                            'label' => 'text.fields.description',
                            'field_type' => CKEditorType::class,
                            'config_name' => 'advanced',
                            'required' => false,
                        ],
                    ],
                ])
            ->end()
            ->with('form_group.additional', ['class' => 'col-md-4', 'name' => false])
                ->add('isActive', CheckboxType::class, [
                    'label' => 'text.fields.is_active',
                    'required' => false,
                ])
            ->end();
    }
}
