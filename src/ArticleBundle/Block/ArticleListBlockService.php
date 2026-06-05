<?php

namespace ArticleBundle\Block;

use AppBundle\Block\AbstractEditableBlockService;
use ArticleBundle\Entity\Article;
use ArticleBundle\Entity\ArticleRepository;
use ArticleBundle\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class ArticleListBlockService extends AbstractEditableBlockService
{
    private ?Registry $doctrine = null;

    public function __construct(Environment $twig)
    {
        parent::__construct($twig);
    }

    public function setDoctrine(Registry $doctrine): void
    {
        $this->doctrine = $doctrine;
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'items_count' => 20,
            'page' => 1,
            'category_slug' => null,
            'category' => null,
            'tag' => null,
            'show_paginator' => true,
            'ajax_paginator' => false,
            'template' => '@Article/Block/large_list.html.twig',
        ]);
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $block = $blockContext->getBlock();
        if (!$block->getEnabled()) {
            return new Response();
        }

        /** @var ArticleRepository $repository */
        $repository = $this->doctrine->getRepository(Article::class);
        $qb = $repository->baseArticleQueryBuilder();

        if ($categorySlug = $blockContext->getSetting('category_slug')) {
            $category = $this->doctrine->getRepository(Category::class)->findOneBy(['slug' => $categorySlug]);
            if ($category) {
                $repository->filterByCategory($qb, $category);
            }
        }
        if ($category = $blockContext->getSetting('category')) {
            $repository->filterByCategory($qb, $category);
        }
        if ($tag = $blockContext->getSetting('tag')) {
            $repository->filterByTag($qb, $tag);
        }

        $paginator = new Pagerfanta(new QueryAdapter($qb, true, false));
        $paginator->setAllowOutOfRangePages(true);
        $paginator->setMaxPerPage((int) $blockContext->getSetting('items_count'));
        $paginator->setCurrentPage((int) $blockContext->getSetting('page'));

        return $this->renderResponse($blockContext->getTemplate(), [
            'articles' => $paginator,
            'block' => $block,
            'settings' => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}