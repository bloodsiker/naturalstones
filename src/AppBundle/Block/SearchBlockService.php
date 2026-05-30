<?php

namespace AppBundle\Block;

use Doctrine\ORM\EntityManager;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use ProductBundle\Entity\Product;
use ProductBundle\Entity\ProductSearchHistory;
use AppBundle\Block\AbstractEditableBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

/**
 * Class SearchBlockService
 */
class SearchBlockService extends AbstractEditableBlockService
{
    const DEFAULT_TEMPLATE = '@App/search/Block/large_list.html.twig';
    const TEMPLATE_AJAX  = '@App/search/Block/large_list_ajax.html.twig';
    const TEMPLATE_PAGINATION  = '@Product/Block/_pagination.html.twig';

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * HeaderBlockService constructor.
     *
     * @param string          $name
     * @param EngineInterface $templating
     * @param EntityManager   $em
     * @param RequestStack    $request
     */
    public function __construct(Environment $twig, EntityManager $em, RequestStack $request)
    {
        parent::__construct($twig);

        $this->em = $em;
        $this->request = $request;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver): void    {
        $resolver->setDefaults([
            'list_type'      => null,
            'search'         => null,
            'items_count'    => 40,
            'page'           => 1,
            'show_paginator' => false,
            'ajax_paginator' => false,
            'template'       => self::DEFAULT_TEMPLATE,
        ]);
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null         $response
     *
     * @return Response
     */
    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response    {
        $block = $blockContext->getBlock();
        if (!$block->getEnabled()) {
            return new Response();
        }

        $request = $this->request->getCurrentRequest();
        $limit = max(1, (int) $blockContext->getSetting('items_count'));
        $isAjax = $request->isXmlHttpRequest();
        $loadMore = $request->get('load_more', false);
        $page = max(1, (int) ($isAjax ? $request->get('page') : $blockContext->getSetting('page')));
        $category = $request->get('category');

        if ($request->get('show_paginator')) {
            $blockContext->setSetting('show_paginator', true);
        }

        $search = trim((string) ($blockContext->getSetting('search') ?: $request->get('search')));

        if ($search) {
            $repository = $this->em->getRepository(Product::class);

            $qb = $repository->baseProductQueryBuilder();
            $qb
                ->innerJoin('p.category', 'category')
                ->addSelect('category');

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

        $template = !is_null($blockContext->getSetting('list_type'))
            ? $blockContext->getSetting('list_type') : $blockContext->getTemplate();

        if ($loadMore) {
            $responseProducts = $this->renderResponse(self::TEMPLATE_AJAX, [
                'products'  => $results ?? [],
                'block'     => $block,
                'settings'  => array_merge($blockContext->getSettings(), $block->getSettings()),
            ], new Response());

            $responsePagination = $this->renderResponse(self::TEMPLATE_PAGINATION, [
                '_route_params' => $request->get('route_params'),
                '_route'    => $request->get('route'),
                'products'  => $results ?? [],
                'block'     => $block,
                'settings'  => array_merge($blockContext->getSettings(), $block->getSettings()),
            ], new Response());

            return new JsonResponse([
                'view' => $responseProducts->getContent(),
                'pagination' => $responsePagination->getContent(),
                'next_page' => (isset($results) && $results) && $results->hasNextPage()
            ]);
        }

        return $this->renderResponse($template, [
            'products'    => $results ?? [],
            'search'      => $search,
            'block'       => $block,
            'settings'    => array_merge($blockContext->getSettings(), $block->getSettings()),
        ]);
    }

    private function saveSearchHistory($search, $ip)
    {
        if (strlen($search) < 2) {
            return;
        }

        $ip = $ip ? substr($ip, 0, 20) : null;

        $qb = $this->em->getRepository(ProductSearchHistory::class)
            ->createQueryBuilder('history')
            ->where('history.search = :search')
            ->andWhere('history.createdAt >= :createdAt')
            ->setParameter('search', $search)
            ->setParameter('createdAt', new \DateTime('-15 minutes'))
            ->setMaxResults(1);

        if ($ip) {
            $qb->andWhere('history.ip = :ip')->setParameter('ip', $ip);
        } else {
            $qb->andWhere('history.ip IS NULL');
        }

        $recentSearch = $qb->getQuery()->getOneOrNullResult();

        if ($recentSearch) {
            return;
        }

        $history = new ProductSearchHistory();
        $history->setSearch($search);
        $history->setIp($ip);

        $this->em->persist($history);
        $this->em->flush();
    }
}
