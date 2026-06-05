<?php

namespace CommentBundle\Block;

use AppBundle\Block\AbstractEditableBlockService;
use CommentBundle\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class ListCommentBlockService extends AbstractEditableBlockService
{
    public const PAGE_LIST = '@Comment/Block/page_comments_list.html.twig';

    private const RESULT_CACHE_TTL = 60;

    public function __construct(
        Environment $twig,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct($twig);
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'list_type' => null,
            'product' => null,
            'show_title' => true,
            'items_count' => 30,
            'page' => 1,
            'show_paginator' => true,
            'template' => '@Comment/Block/comments_list.html.twig',
        ]);
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $block = $blockContext->getBlock();
        if (!$block->getEnabled()) {
            return new Response();
        }

        $product = $blockContext->getSetting('product');

        $qb = $this->em->getRepository(Comment::class)
            ->createQueryBuilder('c')
            ->where('c.isActive = 1')
            ->orderBy('c.createdAt', 'DESC');

        if ($product) {
            $qb->andWhere('c.product = :product')->setParameter('product', $product);
            $results = $qb->getQuery()->enableResultCache(true, self::RESULT_CACHE_TTL)->getResult();
        } else {
            $results = new Pagerfanta(new QueryAdapter($qb, true, false));
            $results->setAllowOutOfRangePages(true);
            $results->setMaxPerPage((int) $blockContext->getSetting('items_count'));
            $results->setCurrentPage((int) $blockContext->getSetting('page'));
        }

        $template = $blockContext->getSetting('list_type') ?? $blockContext->getTemplate();

        return $this->renderResponse($template, [
            'comments' => $results,
            'product' => $product,
            'block' => $block,
            'settings' => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}