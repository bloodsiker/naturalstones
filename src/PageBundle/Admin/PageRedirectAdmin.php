<?php

namespace PageBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\CoreBundle\Validator\ErrorElement;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Admin definition for the PageRedirect class
 */
class PageRedirectAdmin extends AbstractAdmin
{
    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by'      => 'id',
        '_sort_order'   => 'DESC',
    ];

    /**
     * {@inheritdoc}
     */
    public function setTranslationDomain($translationDomain)
    {
        $this->translationDomain = $translationDomain;
        $this->formOptions['translation_domain'] = $translationDomain;
    }

    /**
     * @param ErrorElement $errorElement
     * @param object       $object
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        $fromPath = $this->modelManager->findOneBy($this->getClass(), ['fromPath' => $object->getFromPath()]);

        if (null !== $fromPath && $fromPath->getId() !== $object->getId()) {
            $errorElement
                ->with('fromPath')
                    ->addViolation($this->translator->trans('form_valid.unique', [], $this->translationDomain))
                ->end();
        }
        if (empty($object->getToPath()) && empty($object->getToPage())) {
            $errorElement
                ->with('toPath')
                ->with('toPage')
                    ->addViolation($this->translator->trans('form_valid.start_path', [], $this->translationDomain))
                ->end();
        }
    }

    /**
     * @param FormMapper $formMapper
     *
     * @throws \Exception
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('football.form_group.basic', ['class' => 'col-md-6', 'label' => false])
                ->add('fromPath', TextType::class, [
                    'label' => 'form.label_redirect_from',
                    'required' => true,
                ])
                ->add('type', ChoiceType::class, [
                    'label' => 'form.label_type',
                    'choices' => $this->getTranslationType('type'),
                    'required' => true,
                ])
                ->add('help', TextType::class, [
                    'label' => 'form.label_help',
                    'required' => false,
                ])
            ->end()
            ->with('football.form_group.additional', ['class' => 'col-md-6', 'label' => false])
                ->add('toPage', ModelListType::class, [
                    'label' => 'form.label_redirect_to_page',
                    'required' => false,
                ])
                ->add('toPath', TextType::class, [
                    'label' => 'form.label_redirect_to',
                    'required' => false,
                ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'form.label_enabled',
                'required' => false,
            ])
            ->end()
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('fromPath', null, [
                'label' => 'form.label_redirect_from',
            ])
            ->add('toPath', null, [
                'label' => 'form.label_redirect_to',
            ])
            ->add('isActive', null, [
                'label' => 'form.label_enabled',
            ])
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('type', null, [
                'label' => 'form.label_type',
                'template' => 'PageBundle::PageAdmin/redirect_type.html.twig',
            ])
            ->add('fromPath', null, [
                'label' => 'form.label_redirect_from',
                'template' => 'PageBundle::PageAdmin/redirect_type.html.twig',
            ])
            ->add('toPath', null, [
                'label' => 'form.label_redirect_to',
            ])
            ->add('pageHost', null, [
                'label' => 'form.label_page_host',
                'template' => 'PageBundle::PageAdmin/redirect_type.html.twig',
            ])
            ->add('toPage', null, [
                'label' => 'form.label_redirect_to_page',
            ])
            ->add('isActive', null, [
                'label' => 'form.label_enabled',
                'editable' => true,
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'delete'    => [],
                ],
            ]);
    }

    /**
     * @param string $property
     *
     * @return mixed
     *
     * @throws \Exception
     */
    private function getTranslationType($property)
    {
        $entity = $this->getClass();

        switch ($property) {
            case 'type':
                $types = $entity::getTypeList();
                break;
            default:
                throw new \Exception(sprintf('Unknown property %s', $property));
        }

        foreach ($types as $key => $value) {
            $typeChoice['form.types.'.$value] = $key;
        }

        return $typeChoice;
    }
}
