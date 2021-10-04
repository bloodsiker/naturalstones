<?php

namespace QuizBundle\Admin;

use AppBundle\Traits\FixAdminFormTranslationDomainTrait;

use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\DateTimeFilter;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class QuizResultAdmin
 */
class QuizResultAdmin extends Admin
{
    use FixAdminFormTranslationDomainTrait;

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by'      => 'votedAt',
        '_sort_order'   => 'DESC',
    ];

    /**
     * @return array
     */
    public function getPersistentParameters()
    {
        if (!$this->hasRequest()) {
            return array();
        }

        $parameters = array_filter($this->getRequest()->query->all(), function ($param) {
            return !is_array($param);
        });

        if (!isset($parameters['quiz'])) {
            $parameters['quiz'] = null;
        }

        return $parameters;
    }

    /**
     * @param string $context
     *
     * @return \Sonata\AdminBundle\Datagrid\ProxyQueryInterface
     */
    public function createQuery($context = 'list')
    {
        $query = parent::createQuery();
        $quiz = $this->getRequest()->get('quiz', null);

        if ('list' === $context && $quiz) {
            $query->andWhere(
                $query->expr()->eq($query->getRootAlias().'.quiz', ':quiz')
            );

            $query->setParameter('quiz', (int) $quiz);
        }

        return $query;
    }

    /**
     * @param RouteCollection $collection
     */
    public function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
        $collection->remove('edit');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id', null, [
                'label' => 'quiz_result.fields.id',
            ])
            ->add('quiz', null, [
                'label' => 'quiz_result.fields.quiz',
            ])
            ->add('answer', null, [
                'label' => 'quiz_result.fields.answer',
            ])
            ->add('votedAt', DateTimeFilter::class, [
                'label' => 'quiz_result.fields.voted_at',
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
                'label'     => 'quiz_result.fields.id',
            ])
            ->add('quiz', null, [
                'label'     => 'quiz_result.fields.quiz',
            ])
            ->add('answer', null, [
                'label'     => 'quiz_result.fields.answer',
            ])
            ->add('votedAt', null, [
                'label'     => 'quiz_result.fields.voted_at',
            ])
            ->add('_action', 'actions', [
                'actions'   => [
                    'show'      => [],
                    'delete'    => [],
                ],
            ])
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('quiz_result.form_group.basic', ['class' => 'col-md-6', 'label' => false])
                ->add('quiz', TextType::class, [
                    'label'     => 'quiz_result.fields.quiz',
                ])
                ->add('answer', TextType::class, [
                    'label'     => 'quiz_result.fields.answer',
                ])
                ->add('comment', TextType::class, [
                    'label'     => 'quiz_result.fields.comment',
                ])
            ->end()
            ->with('quiz_result.form_group.info', ['class' => 'col-md-6', 'label' => false])
                ->add('votedAt', null, [
                    'label'     => 'quiz_result.fields.voted_at',
                ])
                ->add('ip', TextType::class, [
                    'label'     => 'quiz_result.fields.ip',
                ])
                ->add('proxy', TextType::class, [
                    'label'     => 'quiz_result.fields.proxy',
                ])
                ->add('hash', TextType::class, [
                    'label'     => 'quiz_result.fields.hash',
                ])
            ->end()
        ;
    }
}
