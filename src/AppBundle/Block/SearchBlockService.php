<?php

namespace AppBundle\Block;

use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use ProductBundle\Entity\Product;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class SearchBlockService extends AbstractEditableBlockService
{
    use SearchHistoryTrait;

    public const DEFAULT_TEMPLATE = '@App/search/Block/large_list.html.twig';
    public const TEMPLATE_AJAX = '@App/search/Block/large_list_ajax.html.twig';
    public const TEMPLATE_PAGINATION = '@Product/Block/_pagination.html.twig';

    public function __construct(
        Environment $twig,
        protected readonly EntityManagerInterface $em,
        private readonly RequestStack $request,
    ) {
        parent::__construct($twig);
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'list_type' => null,
            'search' => null,
            'items_count' => 40,
            'page' => 1,
            'show_paginator' => false,
            'ajax_paginator' => false,
            'template' => self::DEFAULT_TEMPLATE,
        ]);
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $block = $blockContext->getBlock();
        if (!$block->getEnabled()) {
            return new Response();
        }

        $request = $this->request->getCurrentRequest();
        $limit = max(1, (int) $blockContext->getSetting('items_count'));
        $isAjax = $request->isXmlHttpRequest();
        $loadMore = (bool) $request->get('load_more', false);
        $page = max(1, (int) ($isAjax ? $request->get('page') : $blockContext->getSetting('page')));
        $category = $request->get('category');

        if ($request->get('show_paginator')) {
            $blockContext->setSetting('show_paginator', true);
        }

        $search = trim((string) ($blockContext->getSetting('search') ?: $request->get('search')));
        $results = null;

        if ($search) {
            $repository = $this->em->getRepository(Product::class);

            $qb = $repository->baseProductQueryBuilder();
            $qb->innerJoin('p.category', 'category')->addSelect('category');

            $repository->filterByLocale($qb, $search);

            if ($category) {
                $qb = $repository->filterByCategory($qb, $category);
            }

            $qb->orderBy('p.views', 'DESC');

            $results = new Pagerfanta(new QueryAdapter($qb, true, false));
            $results->setAllowOutOfRangePages(true);
            $results->setMaxPerPage($limit);
            $results->setCurrentPage($page);

            if (!$loadMore) {
                $this->saveSearchHistory($search, $request->server->get('REMOTE_ADDR'));
            }
        }

        $template = $blockContext->getSetting('list_type') ?? $blockContext->getTemplate();
        $settings = array_merge($blockContext->getSettings(), $block->getSettings());

        if ($loadMore) {
            $responseProducts = $this->renderResponse(self::TEMPLATE_AJAX, [
                'products' => $results ?? [],
                'block' => $block,
                'settings' => $settings,
            ], new Response());

            $responsePagination = $this->renderResponse(self::TEMPLATE_PAGINATION, [
                '_route_params' => $request->get('route_params'),
                '_route' => $request->get('route'),
                'products' => $results ?? [],
                'block' => $block,
                'settings' => $settings,
            ], new Response());

            return new JsonResponse([
                'view' => $responseProducts->getContent(),
                'pagination' => $responsePagination->getContent(),
                'next_page' => $results && $results->hasNextPage(),
            ]);
        }

        return $this->renderResponse($template, [
            'products' => $results ?? [],
            'search' => $search,
            'block' => $block,
            'settings' => $settings,
        ]);
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }
}