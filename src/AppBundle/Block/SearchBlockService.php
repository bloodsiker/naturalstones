<?php

namespace AppBundle\Block;

use Doctrine\ORM\EntityManager;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use ProductBundle\Entity\Product;
use ProductBundle\Entity\ProductSearchHistory;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

/**
 * Class SearchBlockService
 */
class SearchBlockService extends AbstractAdminBlockService
{
    const DEFAULT_TEMPLATE = 'AppBundle:search/Block:large_list.html.twig';
    const TEMPLATE_AJAX  = 'AppBundle:search/Block:large_list_ajax.html.twig';
    const TEMPLATE_PAGINATION  = 'ProductBundle:Block:_pagination.html.twig';

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
    public function __construct($name, EngineInterface $templating, EntityManager $em, RequestStack $request)
    {
        parent::__construct($name, $templating);

        $this->em = $em;
        $this->request = $request;
    }

    /**
     * @param null $code
     *
     * @return Metadata
     */
    public function getBlockMetadata($code = null)
    {
        return new Metadata(
            $this->getName(),
            (!is_null($code) ? $code : $this->getName()),
            false,
            'AppBundle',
            ['class' => 'fa fa-th-large']
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
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
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $block = $blockContext->getBlock();
        if (!$block->getEnabled()) {
            return new Response();
        }

        $request = $this->request->getCurrentRequest();
        $limit = (int) $blockContext->getSetting('items_count');
        $isAjax = $request->isXmlHttpRequest();
        $loadMore = $request->get('load_more', false);
        $page = $isAjax ? $request->get('page') : $blockContext->getSetting('page');

        if ($request->get('show_paginator')) {
            $blockContext->setSetting('show_paginator', true);
        }

        $search = $blockContext->getSetting('search')
            ? $blockContext->getSetting('search') : $request->get('search');

        if ($search) {
            $repository = $this->em->getRepository(Product::class);

            $qb = $repository->baseProductQueryBuilder();
            $qb = $repository->filterByLocale($qb, $search);
            $qb->orderBy('p.views', 'DESC');

            $results = new Pagerfanta(new QueryAdapter($qb, true, false));
            $results->setAllowOutOfRangePages(true);
            $results->setMaxPerPage($limit);
            $results->setCurrentPage($page);

            $ip = $request->server->get('REMOTE_ADDR');
            $history = new ProductSearchHistory();
            $history->setSearch($search);
            $history->setIp($ip);

            $this->em->persist($history);
            $this->em->flush();
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
}
