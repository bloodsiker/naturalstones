<?php

namespace ProductBundle\Block;

use AppBundle\Block\AbstractEditableBlockService;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use ProductBundle\Block\Traits\HomepageDefaultMethodTrait;
use ProductBundle\Block\Traits\StrictRelationSonataAdminTrait;
use ProductBundle\Entity\Category;
use ProductBundle\Entity\Product;
use ShareBundle\Entity\Colour;
use ShareBundle\Entity\Stone;
use ShareBundle\Entity\Tag;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Form\Mapper\FormMapper;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Meta\MetadataInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\Form\Type\ImmutableArrayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class ListProductBlockService extends AbstractEditableBlockService
{
    use StrictRelationSonataAdminTrait;
    use HomepageDefaultMethodTrait;

    public const SIMILAR_LIST = '@Product/Block/similar_list.html.twig';
    public const BUY_WITH_LIST = '@Product/Block/buy_with_list.html.twig';
    public const TEMPLATE_AJAX = '@Product/Block/large_list_ajax.html.twig';
    public const TEMPLATE_PAGINATION = '@Product/Block/_pagination.html.twig';
    public const TEMPLATE_SELECTED_FILTERS = '@Product/Block/_selected_filters.html.twig';

    public const CATEGORY_NABORI = 5;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * ListGenreBlockService constructor.
     */
    public function __construct(Environment $twig, RequestStack $requestStack)
    {
        parent::__construct($twig);

        $this->requestStack = $requestStack;
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'title' => null,
            'title_url' => null,
            'list_type' => null,
            'items_count' => 20,
            'page' => 1,
            'filter' => false,
            'category' => null,
            'category_slug' => null,
            'tag' => null,
            'colour' => null,
            'stone' => null,
            'stones' => null,
            'who' => null,
            'discount' => false,
            'random' => false,
            'exclude_ids' => null,
            'show_paginator' => false,
            'ajax_paginator' => false,
            'template' => '@Product/Block/large_list.html.twig',
        ]);
    }

    private function buildEntityChoices(string $entityClass, string $labelMethod): array
    {
        $choices = [];
        foreach ($this->doctrine->getRepository($entityClass)->findAll() as $entity) {
            $choices[$entity->$labelMethod()] = $entity->getId();
        }

        return $choices;
    }

    public function configureEditForm(FormMapper $form, BlockInterface $block): void
    {
        foreach (['category', 'tag', 'stone', 'colour'] as $field) {
            $value = $block->getSetting($field);
            if (is_object($value) && method_exists($value, 'getId')) {
                $block->setSetting($field, $value->getId());
            }
        }

        $form->add('settings', ImmutableArrayType::class, [
            'translation_domain' => 'ProductBundle',
            'label' => false,
            'keys' => [
                ['title', TextType::class, [
                    'label' => 'product.block.fields.title',
                    'required' => false,
                ]],
                ['items_count', TextType::class, [
                    'label' => 'product.block.fields.items_count',
                    'required' => false,
                ]],
                ['category', ChoiceType::class, [
                    'label' => 'product.block.fields.category',
                    'required' => false,
                    'placeholder' => '—',
                    'choices' => $this->buildEntityChoices(Category::class, 'getName'),
                ]],
                ['tag', ChoiceType::class, [
                    'label' => 'product.block.fields.tag',
                    'required' => false,
                    'placeholder' => '—',
                    'choices' => $this->buildEntityChoices(Tag::class, 'getName'),
                ]],
                ['stone', ChoiceType::class, [
                    'label' => 'product.block.fields.stone',
                    'required' => false,
                    'placeholder' => '—',
                    'choices' => $this->buildEntityChoices(Stone::class, 'getName'),
                ]],
                ['colour', ChoiceType::class, [
                    'label' => 'product.block.fields.colour',
                    'required' => false,
                    'placeholder' => '—',
                    'choices' => $this->buildEntityChoices(Colour::class, 'getName'),
                ]],
                ['who', ChoiceType::class, [
                    'label' => 'product.block.fields.who',
                    'required' => false,
                    'choices' => array_merge(['' => ''], array_flip(Product::$whois)),
                ]],
                ['discount', CheckboxType::class, [
                    'label' => 'product.block.fields.discount',
                    'required' => false,
                ]],
                ['random', CheckboxType::class, [
                    'label' => 'product.block.fields.random',
                    'required' => false,
                ]],
            ],
        ]);
    }

    public function getMetadata(): MetadataInterface
    {
        return new Metadata('Products', null, null, 'ProductBundle', [
            'class' => 'fa fa-shopping-bag',
        ]);
    }

    /**
     * @throws \Exception
     */
    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $block = $blockContext->getBlock();

        if (!$block->getEnabled()) {
            return new Response();
        }

        $request = $this->requestStack->getCurrentRequest();
        $isAjax = $request->isXmlHttpRequest();
        $loadMore = $request->get('load_more', false);

        $limit = (int) $blockContext->getSetting('items_count');
        $page = $isAjax ? $request->get('page') : $blockContext->getSetting('page');

        if ($request->get('show_paginator')) {
            $blockContext->setSetting('show_paginator', true);
        }

        $repository = $this->doctrine->getRepository(Product::class);
        $repositoryCategory = $this->doctrine->getRepository(Category::class);

        $qb = $repository->baseProductQueryBuilder();

        if ($blockContext->getSetting('category_slug')) {
            $category = $repositoryCategory->findOneBy(['slug' => $blockContext->getSetting('category_slug')]);
            if ($category) {
                $repository->filterByCategory($qb, $category);
            }
        }

        if ($blockContext->getSetting('category')) {
            $repository->filterByCategory($qb, $blockContext->getSetting('category'));
        }

        if ($blockContext->getSetting('tag')) {
            $repository->filterByTag($qb, $blockContext->getSetting('tag'));
        }

        if ($blockContext->getSetting('who')) {
            $repository->filterByWho($qb, $blockContext->getSetting('who'));
        }

        if ($blockContext->getSetting('colour')) {
            $repository->filterByColour($qb, $blockContext->getSetting('colour'));
        }

        if ($blockContext->getSetting('stone')) {
            $repository->filterByStone($qb, $blockContext->getSetting('stone'));
        }

        if ($blockContext->getSetting('stones')) {
            $repository->filterByStones($qb, $blockContext->getSetting('stones'));
        }

        if ($blockContext->getSetting('exclude_ids')) {
            $repository->filterExclude($qb, $blockContext->getSetting('exclude_ids'));
        }

        if ($blockContext->getSetting('discount')) {
            $repository->filterByDiscount($qb);
        }

        if ($blockContext->getSetting('random')) {
            $repository->filterByRand($qb);
        }

        // On ajax filter requests the block is rebuilt with only its id/type, so the
        // `category` setting is lost. Apply the category scope from the request before
        // building the available filter options below so they stay category-scoped.
        if ($request->get('category_slug')) {
            $category = $repositoryCategory->findOneBy(['slug' => $request->get('category_slug')]);
            if ($category) {
                $repository->filterByCategory($qb, $category);
            }
        }

        // The `filter` setting is also lost on ajax requests, so build the filter data
        // whenever it is an ajax filter request as well, otherwise the selected filter
        // chips can never be resolved.
        if (($blockContext->getSetting('filter') || ($isAjax && $request->get('ajax_filter'))) && !$loadMore) {
            $priceBounds = $repository->getPriceBounds($qb);
            $minPrice = $priceBounds['minPrice'];
            $maxPrice = $priceBounds['maxPrice'];

            $stoneArray = $repository->findAvailableStones($qb);
            $colourArray = $repository->findAvailableColours($qb);
        }

        if ($request->get('min_price')) {
            $qb->andWhere('p.price >= :minPrice')->setParameter('minPrice', $request->get('min_price'));
        }

        if ($request->get('max_price')) {
            $qb->andWhere('p.price <= :maxPrice')->setParameter('maxPrice', $request->get('max_price'));
        }

        if ($request->get('sort')) {
            $qb->resetDQLPart('orderBy')->orderBy('p.price', $request->get('sort'));
        }

        if ($request->get('stone')) {
            $repository->filterByStones($qb, explode(',', $request->get('stone')));
        }

        if ($request->get('colour')) {
            $repository->filterByColours($qb, explode(',', $request->get('colour')));
        }

        $paginator = new Pagerfanta(new QueryAdapter($qb, true, false));
        $paginator->setAllowOutOfRangePages(true);
        $paginator->setMaxPerPage((int) $limit);
        $paginator->setCurrentPage(max(1, (int) $page));

        $template = !is_null($blockContext->getSetting('list_type'))
            ? $blockContext->getSetting('list_type') : $blockContext->getTemplate();

        $settings = array_merge($blockContext->getSettings(), $block->getSettings());

        if ($isAjax && $request->get('ajax_filter')) {
            $selectedFilterQuery = $request->request->all();
            foreach (['ajax_filter', 'url', 'route', 'route_params', 'category_slug', 'show_paginator'] as $key) {
                unset($selectedFilterQuery[$key]);
            }
            $selectedFilterQuery = array_filter($selectedFilterQuery, static fn ($value) => null !== $value && '' !== $value);

            $routeParams = $request->get('route_params', []);
            if (!is_array($routeParams)) {
                $routeParams = [];
            }

            $responseProducts = $this->renderResponse(self::TEMPLATE_AJAX, [
                'products' => $paginator,
                'block' => $block,
                'settings' => $settings,
            ], new Response());

            $responsePagination = $this->renderResponse(self::TEMPLATE_PAGINATION, [
                '_route_params' => $routeParams,
                '_route' => $request->get('route'),
                'products' => $paginator,
                'block' => $block,
                'settings' => $settings,
            ], new Response());

            $responseSelectedFilters = $this->renderResponse(self::TEMPLATE_SELECTED_FILTERS, [
                'maxPrice' => $maxPrice ?? 0,
                'minPrice' => $minPrice ?? 0,
                'stones' => $stoneArray ?? [],
                'colours' => $colourArray ?? [],
                'filterBaseUrl' => $request->get('url', ''),
                'selectedFilterQuery' => $selectedFilterQuery,
                'selectedStones' => isset($selectedFilterQuery['stone']) ? explode(',', (string) $selectedFilterQuery['stone']) : [],
                'selectedColours' => isset($selectedFilterQuery['colour']) ? explode(',', (string) $selectedFilterQuery['colour']) : [],
                'selectedMinPrice' => $selectedFilterQuery['min_price'] ?? null,
                'selectedMaxPrice' => $selectedFilterQuery['max_price'] ?? null,
            ], new Response());

            return new JsonResponse([
                'view' => $responseProducts->getContent(),
                'pagination' => $responsePagination->getContent(),
                'selected_filters' => $responseSelectedFilters->getContent(),
                'next_page' => $paginator->hasNextPage(),
                'next_page_number' => $paginator->hasNextPage() ? $paginator->getNextPage() : null,
            ]);
        }

        if ($loadMore) {
            $responseProducts = $this->renderResponse(self::TEMPLATE_AJAX, [
                'products' => $paginator,
                'block' => $block,
                'settings' => $settings,
            ], new Response());

            $responsePagination = $this->renderResponse(self::TEMPLATE_PAGINATION, [
                '_route_params' => $request->get('route_params'),
                '_route' => $request->get('route'),
                'products' => $paginator,
                'block' => $block,
                'settings' => $settings,
            ], new Response());

            return new JsonResponse([
                'view' => $responseProducts->getContent(),
                'pagination' => $responsePagination->getContent(),
                'next_page' => $paginator->hasNextPage(),
            ]);
        }

        return $this->renderResponse($isAjax ? self::TEMPLATE_AJAX : $template, [
            'products' => $paginator,
            'maxPrice' => $maxPrice ?? 0,
            'minPrice' => $minPrice ?? 0,
            'stones' => $stoneArray ?? [],
            'colours' => $colourArray ?? [],
            'block' => $block,
            'settings' => $settings,
        ], $response);
    }
}
