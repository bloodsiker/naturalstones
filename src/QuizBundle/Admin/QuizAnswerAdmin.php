<?php

namespace QuizBundle\Admin;

use AppBundle\Traits\FixAdminFormTranslationDomainTrait;

use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

/**
 * Class QuizAnswerAdmin
 */
class QuizAnswerAdmin extends Admin
{
    use FixAdminFormTranslationDomainTrait;

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by'      => 'id',
        '_sort_order'   => 'DESC',
    ];

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('preview', $this->getRouterIdParameter().'/preview');
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('quiz_answer.form_group.basic', ['class' => 'col-md-6', 'label' => false])
                ->add('title', TextType::class, [
                    'label' => 'quiz_answer.fields.title',
                    'required' => true,
                ])
                ->add('link', UrlType::class, [
                    'label' => 'quiz_answer.fields.link',
                    'required' => false,
                ])
            ->end()
            ->with('quiz_answer.form_group.additional', ['class' => 'col-md-6', 'label' => false])
                ->add('counter', NumberType::class, [
                    'label' => 'quiz_answer.fields.counter',
                    'required' => false,
                    'attr'      => [
                        'readonly'  => true,
                    ],
                ])
                ->add('percent', TextType::class, [
                    'label' => 'quiz_answer.fields.percent',
                    'required' => false,
                    'attr'      => [
                        'readonly'  => true,
                    ],
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
            ->add('id', null, [
                'label' => 'quiz_answer.fields.id',
            ])
            ->add('title', null, [
                'label' => 'quiz_answer.fields.title',
            ])
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, [
                'label'     => 'quiz_answer.fields.id',
            ])
            ->addIdentifier('title', null, [
                'label'     => 'quiz_answer.fields.title',
            ])
            ->add('link', null, [
                'label'     => 'quiz_answer.fields.link',
                'template'  => 'QuizBundle:CRUD:list_answer_link.html.twig',
            ])
            ->add('percent', null, [
                'label'     => 'quiz_answer.fields.percent',
            ])
            ->add('counter', null, [
                'label'     => 'quiz_answer.fields.counter',
            ])
            ->add('_action', 'actions', [
                'actions'   => [
                    'delete'    => [],
                ],
            ])
        ;
    }
}
