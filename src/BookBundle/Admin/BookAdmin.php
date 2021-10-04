<?php

namespace BookBundle\Admin;

use AdminBundle\Admin\BaseAdmin as Admin;
use AdminBundle\Form\Type\TextCounterType;
use BookBundle\Entity\Book;
use Doctrine\ORM\EntityManager;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use SeriesBundle\Entity\Series;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\CoreBundle\Form\Type\CollectionType;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * Class BookAdmin
 */
class BookAdmin extends Admin
{

    /**
     * @var EntityManager $entityManager
     */
    protected $entityManager;

    /**
     * @var array
     */
    protected $datagridValues = [
        '_page'       => 1,
        '_per_page'   => 25,
        '_sort_by'    => 'id',
        '_sort_order' => 'DESC',
    ];

    /**
     * @param EntityManager $entityManager
     *
     * @return EntityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        return $this->entityManager = $entityManager;
    }

    /**
     * @return array
     */
    public function getFormTheme()
    {
        return array_merge(
            parent::getFormTheme(),
            ['BookBundle:Form:admin_fields.html.twig']
        );
    }

    /**
     * @param Book $object
     */
    public function prePersist($object)
    {
        $this->changeEntity($object);
    }

    /**
     * @param $object
     */
    public function preUpdate($object)
    {
        $this->changeEntity($object);
    }

    /**
     * @param $object
     */
    protected function changeEntity($object)
    {
        $genres = $object->getGenres()->getValues();

        $parents = [];

        foreach ($genres as $genre) {
            $count = $this->getCountBook($genre);
            $genre->setCountBook($count);
            $this->entityManager->persist($genre);
            $this->entityManager->flush();

            if ($genre->getParent()) {
                if (!array_key_exists($genre->getParent()->getId(), $parents)) {
                    $parents[] = $genre->getParent()->getId();
                    $parent = $genre->getParent();

                    $count = $this->getCountBook($parent);
                    $parent->setCountBook($count);
                    $this->entityManager->persist($parent);
                    $this->entityManager->flush();
                }
            }
        }
    }

    /**
     * @param $genre
     *
     * @return int
     */
    protected function getCountBook($genre): int
    {
        $repo = $this->entityManager->getRepository(Book::class);

        $qb = $repo->baseBookQueryBuilder();
        $countBook = $repo->filterByGenre($qb, $genre)
            ->distinct()
            ->resetDQLPart('select')
            ->addSelect('count(b) as count')
            ->getQuery()->getSingleResult();

        return $countBook['count'];
    }

    /**
     * @param ErrorElement $errorElement
     * @param mixed        $object
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('description')
                ->addConstraint(new NotNull())
            ->end()
            ->with('genres')
                ->addConstraint(new NotNull())
            ->end()
        ;
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('acl');

        $collection->add('preview', 'preview');
        $collection->add('related_by_tags', 'related-by-tags');
        $collection->add('find_tags_in_text', 'find-tags-in-text');
        $collection->add('search_book', 'search-book');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, [
                'label' => 'book.fields.id',
            ])
            ->add('poster', null, [
                'label'     => 'book.fields.poster',
                'template'  => 'BookBundle:Admin:list_fields.html.twig',
            ])
            ->addIdentifier('name', null, [
                'label' => 'book.fields.name',
                'template'  => 'BookBundle:Admin:list_fields.html.twig',
            ])
            ->add('genres', null, [
                'label' => 'book.fields.genres',
                'template'  => 'BookBundle:Admin:list_fields.html.twig',
            ])
            ->add('files', null, [
                'label' => 'book.fields.files',
                'template'  => 'BookBundle:Admin:list_fields.html.twig',
            ])
            ->add('isAllowDownload', null, [
                'label' => 'book.fields.is_allow_download',
                'editable'  => true,
            ])
            ->add('isActive', null, [
                'label' => 'book.fields.is_active',
                'editable'  => true,
            ])
            ->add('createdAt', null, [
                'label' => 'book.fields.created_at',
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'preview' => ['template' => 'BookBundle:CRUD:list__action_preview.html.twig'],
                    'edit' => [],
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
            ->add('name', null, [
                'label' => 'book.fields.name',
            ])
            ->add('genres', null, [
                'label' => 'book.fields.genres',
            ])
            ->add('isAllowDownload', null, [
                'label' => 'book.fields.is_allow_download',
            ])
            ->add('isActive', null, [
                'label' => 'book.fields.is_active',
            ])
            ->add('createdAt', DateFilter::class, [
                'label'         => 'book.fields.created_at',
                'field_type'    => DateTimePickerType::class,
                'field_options' => array('format' => 'dd.MM.yyyy'),
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $context = $this->getPersistentParameter('context');

        $formMapper
            ->with('form_group.basic', ['class' => 'col-md-8', 'name' => null])
                ->add('name', TextCounterType::class, [
                    'label' => 'book.fields.name',
                ])
                ->add('description', CKEditorType::class, [
                    'label' => 'book.fields.description',
                    'config_name' => 'advanced',
                    'required' => true,
                    'attr' => [
                        'rows' => 5,
                    ],
                ])
                ->add('slug', TextType::class, [
                    'label' => 'book.fields.slug',
                    'required' => false,
                    'attr' => ['readonly' => !$this->getSubject()->getId() ? false : true],
                ])
                ->add('bookHasFiles', CollectionType::class, [
                    'label' => 'book.fields.files',
                    'required' => false,
                    'constraints' => new Valid(),
                    'by_reference' => false,
                ], [
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable' => 'orderNum',
                    'link_parameters' => ['context' => $context],
                    'admin_code' => 'sonata.admin.book_has_files',
                ])
                ->add('bookHasRelated', CollectionType::class, [
                    'label' => 'book.fields.book_related',
                    'required' => false,
                    'constraints' => new Valid(),
                    'by_reference' => false,
                ], [
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable' => 'orderNum',
                    'link_parameters' => ['context' => $context],
                    'admin_code' => 'sonata.admin.book_has_related',
                ])
            ->end()
            ->with('form_group.additional', ['class' => 'col-md-4', 'name' => null])
                ->add('isActive', null, [
                    'label' => 'book.fields.is_active',
                    'required' => false,
                ])
                ->add('isAllowDownload', null, [
                    'label' => 'book.fields.is_allow_download',
                    'required' => false,
                ])
                ->add('poster', ModelListType::class, [
                    'label' => 'book.fields.poster',
                    'required' => false,
                ])
                ->add('isbn', TextType::class, [
                    'label' => 'book.fields.isbn',
                    'required' => false,
                ])
                ->add('pages', IntegerType::class, [
                    'label' => 'book.fields.pages',
                    'required' => false,
                ])
                ->add('restrictAge', IntegerType::class, [
                    'label' => 'book.fields.restrict_age',
                    'required' => false,
                ])
                ->add('year', IntegerType::class, [
                    'label' => 'book.fields.year',
                    'required' => false,
                ])
                ->add('genres', ModelAutocompleteType::class, [
                    'label' => 'book.fields.genres',
                    'required' => true,
                    'property' => 'name',
                    'multiple' => true,
                    'btn_add' => 'book.buttons.link_add',
                    'attr' => ['class' => 'form-control'],
                    'callback' => function($admin, $property, $value) {
                        if (!$value) {
                            return;
                        }

                        $datagrid = $admin->getDatagrid();
                        $queryBuilder = $datagrid->getQuery();
                        $queryBuilder->andWhere($queryBuilder->getRootAlias() . '.parent IS NOT NULL');
                        $datagrid->setValue($property, null, $value);

                        return true;
                    },
                    'btn_catalogue' => $this->translationDomain,
                    'minimum_input_length' => 2,
                ])
                ->add('tags', ModelAutocompleteType::class, [
                    'label' => 'book.fields.tags',
                    'required' => false,
                    'property' => 'name',
                    'multiple' => true,
                    'btn_add' => 'book.buttons.link_add',
                    'attr' => ['class' => 'form-control'],
                    'btn_catalogue' => $this->translationDomain,
                    'minimum_input_length' => 2,
                ])
                ->add('rating', TextType::class, [
                    'label' => 'book.fields.rating',
                    'required' => false,
                    'attr' => ['readonly' => true],
                ])
                ->add('views', IntegerType::class, [
                    'label' => 'book.fields.views',
                    'required' => false,
                    'attr' => ['readonly' => true],
                ])
                ->add('download', IntegerType::class, [
                    'label' => 'book.fields.download',
                    'required' => false,
                    'attr' => ['readonly' => true],
                ])
                ->add('updatedAt', DateTimePickerType::class, [
                    'label'     => 'book.fields.updated_at',
                    'required' => false,
                    'format' => 'dd-MM-YYYY HH:mm',
                    'attr' => ['readonly' => true],
                ])
            ->end()
        ;
    }
}
