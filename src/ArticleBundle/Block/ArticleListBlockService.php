<?php

namespace ArticleBundle\Block;

use ArticleBundle\Entity\Article;
use ArticleBundle\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use InformationBundle\Entity\Information;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use ProductBundle\Entity\Product;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ArticleListBlockService
 */
class ArticleListBlockService extends AbstractAdminBlockService
{
    /**
     * @var Registry $doctrine
     */
    private $doctrine;

    /**
     * ArticleListBlockService constructor.
     *
     * @param string          $name
     * @param EngineInterface $templating
     */
    public function __construct($name, EngineInterface $templating)
    {
        parent::__construct($name, $templating);
    }

    /**
     * @param Registry $doctrine
     */
    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
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
            'ArticleBundle',
            ['class' => 'fa fa-code']
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'items_count'    => 20,
            'page'           => 1,
            'category_slug'  => null,
            'category'       => null,
            'tag'            => null,
            'show_paginator' => true,
            'ajax_paginator' => false,
            'template'       => 'ArticleBundle:Block:large_list.html.twig',
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

        $limit = (int) $blockContext->getSetting('items_count');
        $page = $blockContext->getSetting('page');

        $repository = $this->doctrine->getRepository(Article::class);
        $repositoryCategory = $this->doctrine->getRepository(Category::class);

        $qb = $repository->baseArticleQueryBuilder();

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


        $paginator = new Pagerfanta(new QueryAdapter($qb, true, false));
        $paginator->setAllowOutOfRangePages(true);
        $paginator->setMaxPerPage((int) $limit);
        $paginator->setCurrentPage((int) $page);

        return $this->renderResponse($blockContext->getTemplate(), [
            'articles'     => $paginator,
            'block'        => $block,
            'settings'     => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}
