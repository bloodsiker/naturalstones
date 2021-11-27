<?php

namespace ProductBundle\Block;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use ProductBundle\Entity\Category;
use ProductBundle\Entity\Product;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ListProductBlockService
 */
class ListProductBlockService extends AbstractAdminBlockService
{
    const SIMILAR_LIST = 'ProductBundle:Block:similar_list.html.twig';
    const BUY_WITH_LIST = 'ProductBundle:Block:buy_with_list.html.twig';
    const TEMPLATE_AJAX  = 'ProductBundle:Block:large_list_ajax.html.twig';

    /**
     * @var Registry $doctrine
     */
    protected $doctrine;

    /**
     * @var RequestStack $requestStack
     */
    protected $requestStack;

    /**
     * ListGenreBlockService constructor.
     *
     * @param string          $name
     * @param EngineInterface $templating
     * @param Registry        $doctrine
     * @param RequestStack    $requestStack
     */
    public function __construct($name, EngineInterface $templating, Registry $doctrine, RequestStack $requestStack)
    {
        parent::__construct($name, $templating);

        $this->doctrine = $doctrine;
        $this->requestStack = $requestStack;
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
            'ProductBundle',
            ['class' => 'fa fa-th-large']
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'title'            => null,
            'list_type'        => null,
            'items_count'      => 20,
            'page'             => 1,
            'category'         => null,
            'category_slug'    => null,
            'tag'              => null,
            'colour'           => null,
            'stone'            => null,
            'who'              => null,
            'exclude_ids'      => null,
            'show_paginator'   => true,
            'ajax_paginator'   => false,
            'template'         => 'ProductBundle:Block:large_list.html.twig',
        ]);
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null         $response
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $block = $blockContext->getBlock();

        if (!$block->getEnabled()) {
            return new Response();
        }

        $request = $this->requestStack->getCurrentRequest();
        $isAjax = $request->isXmlHttpRequest();

        $limit = (int) $blockContext->getSetting('items_count');
        $page = $isAjax ? $request->get('page') : $blockContext->getSetting('page');

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

        if ($blockContext->getSetting('exclude_ids')) {
            $repository->filterExclude($qb, $blockContext->getSetting('exclude_ids'));
        }

        $paginator = new Pagerfanta(new DoctrineORMAdapter($qb, true, false));
        $paginator->setAllowOutOfRangePages(true);
        $paginator->setMaxPerPage((int) $limit);
        $paginator->setCurrentPage((int) $page);

        $template = !is_null($blockContext->getSetting('list_type'))
            ? $blockContext->getSetting('list_type') : $blockContext->getTemplate();

        return $this->renderResponse($request->isXmlHttpRequest() ? self::TEMPLATE_AJAX : $template, [
            'products'  => $paginator,
            'block'     => $block,
            'settings'  => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}
