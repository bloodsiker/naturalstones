<?php

namespace QuizBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class QuizHasAnswerAdmin
 */
class QuizHasAnswerAdmin extends Admin
{
    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('answer', null, [
                'label' => 'quiz_has_answer.fields.answer',
            ], ['link_parameters' => [ 'mode' => 'multiple'] ])
            ->add('answer.percent', null, [
                'label' => 'quiz_has_answer.fields.percent',
            ])
            ->add('answer.counter', null, [
                'label' => 'quiz_has_answer.fields.counter',
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
            ->add('answer', ModelListType::class, [
                'label' => 'quiz_has_answer.fields.answer',
                'required' => true,
                'btn_list' => false,
                'btn_delete' => false,
            ], ['link_parameters' => $linkParameters]);

        if ($this->getSubject() && $this->getSubject()->getId()) {
            $formMapper
                ->add('answer.percent', TextType::class, [
                    'label' => 'quiz_has_answer.fields.percent',
                    'required' => false,
                    'empty_data' => $this->getSubject()->getAnswer()->getPercent(),
                    'attr' => [
                        'readonly' => true,
                        'disabled' => true,
                    ],
                ])
                ->add('answer.counter', TextType::class, [
                    'label' => 'quiz_has_answer.fields.counter',
                    'required' => false,
                    'empty_data' => $this->getSubject()->getAnswer()->getCounter(),
                    'attr' => [
                        'readonly' => true,
                        'disabled' => true,
                    ],
                ])
            ;
        }

        $formMapper
            ->add('orderNum', HiddenType::class, array(
                'label' => 'quiz_has_answer.fields.order_num',
            ))
        ;
    }
}
