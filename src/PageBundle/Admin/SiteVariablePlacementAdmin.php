<?php

namespace PageBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Class SiteVariablePlacementAdmin
 */
class SiteVariablePlacementAdmin extends AbstractAdmin
{
    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->remove('acl');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id', null, [
                'label' => 'list.label_id',
            ])
            ->addIdentifier('alias', null, [
                'label' => 'list.label_alias',
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => [],
                ],
            ])
        ;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('alias', TextType::class, [
                'label' => 'form.label_alias',
                'required' => true,
                'attr' => ['style' => 'width:300px'],
                'help' => $this->getTranslator()->trans(
                    'form.validation.regexp',
                    ['%rule%' => 'a-z 0-9 -'],
                    $this->getTranslationDomain()
                ),
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 3, 'max' => 32]),
                    new Regex('/^[a-z0-9\-]+$/'),
                ],
            ])
        ;
    }
}
