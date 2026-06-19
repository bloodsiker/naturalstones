<?php

namespace OrderBundle\Admin;

use AdminBundle\Admin\BaseAdmin as Admin;
use OrderBundle\Entity\PromoCode;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PromoCodeAdmin extends Admin
{
    protected $datagridValues = [
        '_page'       => 1,
        '_per_page'   => 25,
        '_sort_by'    => 'id',
        '_sort_order' => 'DESC',
    ];

    private function typeChoices(): array
    {
        return [
            'promo_code.type.fixed'   => PromoCode::TYPE_FIXED,
            'promo_code.type.percent' => PromoCode::TYPE_PERCENT,
        ];
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id', null, ['label' => 'promo_code.fields.id'])
            ->addIdentifier('code', null, ['label' => 'promo_code.fields.code'])
            ->add('type', 'choice', [
                'label'    => 'promo_code.fields.type',
                'choices'  => $this->typeChoices(),
                'catalogue' => $this->getTranslationDomain(),
            ])
            ->add('value', null, ['label' => 'promo_code.fields.value'])
            ->add('expiresAt', null, ['label' => 'promo_code.fields.expires_at'])
            ->add('usageLimit', null, ['label' => 'promo_code.fields.usage_limit'])
            ->add('usageCount', null, ['label' => 'promo_code.fields.usage_count'])
            ->add('isActive', null, [
                'label'    => 'promo_code.fields.is_active',
                'editable' => true,
            ])
            ->add('createdAt', null, ['label' => 'promo_code.fields.created_at'])
            ->add('_action', 'actions', [
                'actions' => ['edit' => [], 'delete' => []],
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('code', null, ['label' => 'promo_code.fields.code'])
            ->add('type', null, ['label' => 'promo_code.fields.type'])
            ->add('isActive', null, ['label' => 'promo_code.fields.is_active']);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('form_group.basic', ['class' => 'col-md-8', 'name' => false])
                ->add('code', TextType::class, [
                    'label' => 'promo_code.fields.code',
                    'attr'  => ['style' => 'text-transform:uppercase'],
                    'help'  => 'promo_code.fields.code_help',
                ])
                ->add('type', ChoiceType::class, [
                    'label'                     => 'promo_code.fields.type',
                    'choices'                   => $this->typeChoices(),
                    'choice_translation_domain' => $this->getTranslationDomain(),
                ])
                ->add('value', NumberType::class, [
                    'label' => 'promo_code.fields.value',
                    'help'  => 'promo_code.fields.value_help',
                    'scale' => 2,
                ])
            ->end()
            ->with('form_group.additional', ['class' => 'col-md-4', 'name' => false])
                ->add('isActive', null, [
                    'label'    => 'promo_code.fields.is_active',
                    'required' => false,
                ])
                ->add('expiresAt', DateTimePickerType::class, [
                    'label'    => 'promo_code.fields.expires_at',
                    'required' => false,
                    'format'   => 'dd-MM-yyyy HH:mm',
                    'help'     => 'promo_code.fields.expires_at_help',
                ])
                ->add('usageLimit', IntegerType::class, [
                    'label'    => 'promo_code.fields.usage_limit',
                    'required' => false,
                    'help'     => 'promo_code.fields.usage_limit_help',
                    'attr'     => ['min' => 1],
                ])
                ->add('usageCount', IntegerType::class, [
                    'label'    => 'promo_code.fields.usage_count',
                    'required' => false,
                    'attr'     => ['readonly' => true],
                ])
            ->end();
    }
}