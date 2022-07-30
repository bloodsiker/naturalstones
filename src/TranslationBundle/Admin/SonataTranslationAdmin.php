<?php

namespace TranslationBundle\Admin;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

use Lexik\Bundle\TranslationBundle\Manager\TransUnitManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class SonataTranslationAdmin
 */
class SonataTranslationAdmin extends AbstractAdmin
{
    /**
     * @var TransUnitManagerInterface
     */
    protected $transUnitManager;

    /**
     * @var array
     */
    protected $editableOptions;

    /**
     * @var array
     */
    protected $defaultSelections = array();

    /**
     * @var array
     */
    protected $emptyFieldPrefixes = array();

    /**
     * @var array
     */
    protected $filterLocales = array();

    /**
     * @var array
     */
    protected $managedLocales = array();

    /**
     * @return array
     */
    public function getBatchActions()
    {
        $label = $this->getLabelTranslatorStrategy()
            ->getLabel('download', 'batch', $this->getTranslationDomain());

        $actions = parent::getBatchActions();
        $actions['download'] = array(
            'label'                 => $label,
            'ask_confirmation'      => false,
            'translation_domain'    => $this->getTranslationDomain(),
        );

        return $actions;
    }

    /**
     * @param array $options
     */
    public function setEditableOptions(array $options)
    {
        $this->editableOptions = $options;
    }

    /**
     * @param TransUnitManagerInterface $translationManager
     */
    public function setTransUnitManager(TransUnitManagerInterface $translationManager)
    {
        $this->transUnitManager = $translationManager;
    }

    /**
     * @param array $managedLocales
     */
    public function setManagedLocales(array $managedLocales)
    {
        $this->managedLocales = $managedLocales;
    }

    /**
     * @return array
     */
    public function getEmptyFieldPrefixes()
    {
        return $this->emptyFieldPrefixes;
    }

    /**
     * @return array
     */
    public function getDefaultSelections()
    {
        return $this->defaultSelections;
    }


    /**
     * @return bool
     */
    public function getNonTranslatedOnly()
    {
        return array_key_exists('nonTranslatedOnly', $this->getDefaultSelections())
            && (bool) $this->defaultSelections['nonTranslatedOnly'];
    }

    /**
     * @param array $selections
     */
    public function setDefaultSelections(array $selections)
    {
        $this->defaultSelections = $selections;
    }

    /**
     * @param array $prefixes
     */
    public function setEmptyPrefixes(array $prefixes)
    {
        $this->emptyFieldPrefixes = $prefixes;
    }

    /**
     * @return array
     */
    public function getFilterParameters()
    {
        $this->datagridValues = array_merge(
            array(
                'domain' => array(
                    'value' => $this->getDefaultDomain(),
                ),
            ),
            $this->datagridValues

        );

        return parent::getFilterParameters();
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getTemplate($name)
    {
        if ('layout' === $name) {
            return $this->getOriginalTemplate($name);
        }

        if ('list' === $name) {
            return 'IbrowsSonataTranslationBundle:CRUD:list.html.twig';
        }

        return parent::getTemplate($name);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getOriginalTemplate($name)
    {
        return parent::getTemplate($name);
    }

    /**
     * {@inheritdoc}
     */
    public function buildDatagrid()
    {
        if ($this->datagrid) {
            return;
        }

        $filterParameters = $this->getFilterParameters();

        // transform _sort_by from a string to a FieldDescriptionInterface for the datagrid.
        if (isset($filterParameters['locale']) && is_array($filterParameters['locale'])) {
            $this->filterLocales = array_key_exists('value', $filterParameters['locale'])
                ? $filterParameters['locale']['value'] : $this->managedLocales;
        }

        parent::buildDatagrid();
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add(
                'locale',
                'doctrine_orm_callback',
                array(
                    'callback'      => function (ProxyQuery $queryBuilder, $alias, $field, $options) {
                        /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
                        if (!isset($options['value']) || empty($options['value'])) {
                            return;
                        }
                        // use on to filter locales
                        $this->joinTranslations($queryBuilder, $alias, $options['value']);
                    },
                    'field_options' => array(
                        'choices'  => $this->formatLocales($this->managedLocales),
                        'required' => false,
                        'multiple' => true,
                        'expanded' => false,
                    ),
                    'field_type'    => ChoiceType::class,
                )
            )
//            ->add(
//                'show_non_translated_only',
//                'doctrine_orm_callback',
//                array
//                (
//                    'callback'      => function (ProxyQuery $queryBuilder, $alias, $field, $options) {
//                        /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
//                        if (!isset($options['value']) || empty($options['value']) || false === $options['value']) {
//                            return;
//                        }
//                        $this->joinTranslations($queryBuilder, $alias);
//
//                        foreach ($this->getEmptyFieldPrefixes() as $prefix) {
//                            if (empty($prefix)) {
//                                $queryBuilder->orWhere('translations.content IS NULL');
//                            } else {
//                                $queryBuilder->orWhere('translations.content LIKE :content')->setParameter(
//                                    'content',
//                                    $prefix.'%'
//                                );
//                            }
//
//                        }
//                    },
//                    'field_options' => array(
//                        'required' => true,
//                        'value'    => $this->getNonTranslatedOnly(),
//                    ),
//                    'field_type'    => CheckboxType::class,
//                )
//            )
            ->add('key', 'doctrine_orm_string')
            ->add(
                'domain',
                'doctrine_orm_choice',
                array(
                    'field_options' => array(
                        'choices'     => $this->getDomains(),
                        'required'    => true,
                        'multiple'    => false,
                        'expanded'    => false,
                    ),
                    'field_type'    => ChoiceType::class,
                )
            )
            ->add(
                'content',
                'doctrine_orm_callback',
                array
                (
                    'callback'   => function (ProxyQuery $queryBuilder, $alias, $field, $options) {
                        /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
                        if (!isset($options['value']) || empty($options['value'])) {
                            return;
                        }
                        $this->joinTranslations($queryBuilder, $alias);
                        $queryBuilder->andWhere('translations.content LIKE :content')->setParameter(
                            'content',
                            '%'.$options['value'].'%'
                        );
                    },
                    'field_type' => TextType::class,
                    'label'      => 'content',
                )
            );
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('clear_cache')
            ->add('create_trans_unit');
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->add('id', 'integer')
            ->add('key', 'string')
            ->add('domain', 'string');

        $localesToShow = count($this->filterLocales) > 0 ? $this->filterLocales : $this->managedLocales;

        foreach ($localesToShow as $locale) {
            $fieldDescription = $this->modelManager->getNewFieldDescriptionInstance($this->getClass(), $locale);
            $fieldDescription->setTemplate(
                'IbrowsSonataTranslationBundle:CRUD:base_inline_translation_field.html.twig'
            );
            $fieldDescription->setOption('locale', $locale);
            $fieldDescription->setOption('editable', $this->editableOptions);
            $list->add($fieldDescription);
        }
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form)
    {
        $subject = $this->getSubject();

        if (null === $subject->getId()) {
            $subject->setDomain($this->getDefaultDomain());
        }

        $form
            ->add('key', TextType::class)
            ->add('domain', ChoiceType::class, [
                'expanded'          => false,
                'multiple'          => false,
                'required'          => true,
                'choices'           => $this->getDomains(),
                'choices_as_values' => true,
            ]);
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->getConfigurationPool()->getContainer();
    }

    /**
     * @return string
     */
    protected function getDefaultDomain()
    {
        return $this->getContainer()->getParameter('ibrows_sonata_translation.defaultDomain');
    }

    /**
     * @return array
     */
    private function getDomains()
    {
        $defaultDomain = $this->getDefaultDomain();

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getManagerForClass('Lexik\Bundle\TranslationBundle\Entity\File');

        $domains = array();
        $domainsQueryResult = $em->createQueryBuilder()
            ->select('DISTINCT t.domain')->from('\Lexik\Bundle\TranslationBundle\Entity\File', 't')
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        array_walk_recursive(
            $domainsQueryResult,
            function ($domain) use (&$domains) {
                $domains[$domain] = $domain;
            }
        );

        ksort($domains);

        return $domains ? $domains : [$defaultDomain => $defaultDomain];
    }

    /**
     * @param ProxyQuery $queryBuilder
     * @param string     $alias
     * @param array|null $locales
     */
    private function joinTranslations(ProxyQuery $queryBuilder, $alias, array $locales = null)
    {
        $alreadyJoined = false;
        $joins = $queryBuilder->getDQLPart('join');
        if (array_key_exists($alias, $joins)) {
            $joins = $joins[$alias];
            foreach ($joins as $join) {
                if (strpos($join->__toString(), "$alias.translations ")) {
                    $alreadyJoined = true;
                }
            }
        }
        if (!$alreadyJoined) {
            /** @var QueryBuilder $queryBuilder */
            if ($locales) {
                $queryBuilder->leftJoin(sprintf('%s.translations', $alias), 'translations', 'WITH', 'translations.locale = :locales');
                $queryBuilder->setParameter('locales', $locales);
            } else {
                $queryBuilder->leftJoin(sprintf('%s.translations', $alias), 'translations');
            }
        }
    }

    /**
     * @param array $locales
     *
     * @return array
     */
    private function formatLocales(array $locales)
    {
        $formattedLocales = array();
        array_walk_recursive(
            $locales,
            function ($language) use (&$formattedLocales) {
                $formattedLocales[$language] = $language;
            }
        );

        return $formattedLocales;
    }
}
