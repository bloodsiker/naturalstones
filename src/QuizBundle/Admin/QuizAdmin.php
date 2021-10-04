<?php

namespace QuizBundle\Admin;

use AppBundle\Traits\FixAdminFormTranslationDomainTrait;

use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Form\Type\CollectionType;

use Sonata\DoctrineORMAdminBundle\Filter\DateTimeFilter;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * Class QuizAdmin
 */
class QuizAdmin extends Admin
{
    use FixAdminFormTranslationDomainTrait;

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by'      => 'createdAt',
        '_sort_order'   => 'DESC',
    ];

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $context = $this->getPersistentParameter('context');

        $formMapper
            ->with('quiz.form_group.basic', ['class' => 'col-md-6', 'label' => false])
                ->add('title', TextType::class, [
                    'label' => 'quiz.fields.title',
                    'required' => true,
                ])
                ->add('createdAt', DateTimeType::class, [
                    'label' => 'quiz.fields.created_at',
                    'widget' => 'single_text',
                    'format' => 'dd.MM.yyyy HH:mm:ss',
                    'attr'      => [
                        'readonly'  => true,
                    ],
                ])
            ->end()
            ->with('quiz.form_group.additional', ['class' => 'col-md-6', 'label' => false])
                ->add('isActive', CheckboxType::class, [
                    'label'    => 'quiz.fields.is_active',
                    'required' => false,
                ])
                ->add('votedType', ChoiceType::class, [
                    'label'     => 'quiz.fields.voted_type',
                    'choices'   => $this->getTranslationVotedMode(),
                    'expanded'  => false,
                    'multiple'  => false,
                    'required'  => true,
                ])
                ->add('votedCount', NumberType::class, [
                    'label' => 'quiz.fields.voted_count',
                    'required' => false,
                    'attr'      => [
                        'readonly'  => true,
                    ],
                ])
            ->end()
            ->with('quiz.form_group.answers', ['class' => 'col-md-12', 'label' => false])
                ->add('quizHasAnswer', CollectionType::class, [
                    'label' => 'quiz.fields.answers',
                    'required' => true,
                    'constraints' => new Valid(),
                    'by_reference' => false,
                ], [
                    'edit'      => 'inline',
                    'inline'    => 'table',
                    'sortable'  => 'orderNum',
                    'link_parameters' => ['context' => $context],
                    'admin_code' => 'sonata.admin.quiz_has_answer',
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
                'label' => 'quiz.fields.id',
            ])
            ->add('title', null, [
                'label' => 'quiz.fields.title',
            ])
            ->add('isActive', null, [
                'label' => 'quiz.fields.is_active',
            ])
            ->add('votedType', null, [
                'label' => 'quiz.fields.voted_type',
            ], ChoiceType::class, [
                'choices' => $this->getTranslationVotedMode(),
                'choice_translation_domain' => $this->getTranslationDomain(),
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('createdAt', DateTimeFilter::class, [
                'label' => 'quiz.fields.created_at',
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
                'label'     => 'quiz.fields.id',
            ])
            ->addIdentifier('title', null, [
                'label'     => 'quiz.fields.title',
            ])
            ->add('isActive', null, [
                'label'     => 'quiz.fields.is_active',
                'editable'  => true,
            ])
            ->add('votedType', 'choice', [
                'label'     => 'quiz.fields.voted_type',
                'choices'   => array_flip($this->getTranslationVotedMode()),
                'catalogue' => $this->getTranslationDomain(),
                'editable'  => true,
            ])
            ->add('createdAt', null, [
                'label'     => 'quiz.fields.created_at',
            ])
            ->add('_action', 'actions', [
                'actions'   => [
                    'edit'   => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    /**
     * @return array
     */
    private function getTranslationVotedMode()
    {
        $quizEntity = $this->getClass();
        $modes = $quizEntity::getVotedMode();

        $modeChoice = [];
        foreach ($modes as $key => $value) {
            $modeChoice['quiz.fields.modes.'.$value] = $key;
        }

        return $modeChoice;
    }

}
