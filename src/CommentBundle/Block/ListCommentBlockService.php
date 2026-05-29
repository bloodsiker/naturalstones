<?php

namespace CommentBundle\Block;

use CommentBundle\Entity\Comment;
use Doctrine\ORM\EntityManager;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ListCommentBlockService
 */
class ListCommentBlockService extends AbstractBlockService
{
    const PAGE_LIST = '@Comment/Block/page_comments_list.html.twig';

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * ListGenreBlockService constructor.
     *
     * @param string          $name
     * @param EngineInterface $templating
     * @param EntityManager   $em
     */
    public function __construct(Environment $twig, EntityManager $em)
    {
        parent::__construct($twig);

        $this->em = $em;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver): void    {
        $resolver->setDefaults([
            'list_type'      => null,
            'product'        => null,
            'show_title'     => true,
            'items_count'    => 30,
            'page'           => 1,
            'show_paginator' => true,
            'template'       => '@Comment/Block/comments_list.html.twig',
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

        $limit = (int) $blockContext->getSetting('items_count');
        $page = (int) $blockContext->getSetting('page');
        $product = $blockContext->getSetting('product');

        $repository = $this->em->getRepository(Comment::class);

        $qb = $repository->createQueryBuilder('c');
        $qb
            ->where('c.isActive = 1')
            ->orderBy('c.createdAt', 'DESC');

        if ($product) {
            $qb->andWhere('c.product = :product')->setParameter('product', $product);
            $results = $qb->getQuery()->enableResultCache(true, 60)->getResult();
        } else {
            $results = new Pagerfanta(new QueryAdapter($qb, true, false));
            $results->setAllowOutOfRangePages(true);
            $results->setMaxPerPage($limit);
            $results->setCurrentPage($page);
        }

        $template = !is_null($blockContext->getSetting('list_type'))
            ? $blockContext->getSetting('list_type') : $blockContext->getTemplate();

        return $this->renderResponse($template, [
            'comments'  => $results,
            'product'   => $product,
            'block'     => $block,
            'settings'  => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}
