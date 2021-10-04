<?php

namespace CommentBundle\Block;

use CommentBundle\Entity\Comment;
use Doctrine\ORM\EntityManager;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ListCommentBlockService
 */
class ListCommentBlockService extends AbstractAdminBlockService
{
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
    public function __construct($name, EngineInterface $templating, EntityManager $em)
    {
        parent::__construct($name, $templating);

        $this->em = $em;
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
            'CommentBundle',
            ['class' => 'fa fa-code']
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'book'           => null,
            'show_title'     => true,
            'items_count'    => 30,
            'page'           => 1,
            'show_paginator' => true,
            'template'       => 'CommentBundle:Block:comments_list.html.twig',
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
        $page = (int) $blockContext->getSetting('page');
        $book = $blockContext->getSetting('book');

        $repository = $this->em->getRepository(Comment::class);

        $qb = $repository->createQueryBuilder('c');
        $qb
            ->where('c.isActive = 1')
            ->orderBy('c.createdAt', 'DESC');

        if ($book) {
            $qb->andWhere('c.book = :book')->setParameter('book', $book);
            $results = $qb->getQuery()->useResultCache(true, 60)->getResult();
        } else {
            $results = new Pagerfanta(new DoctrineORMAdapter($qb, true, false));
            $results->setAllowOutOfRangePages(true);
            $results->setMaxPerPage($limit);
            $results->setCurrentPage($page);
        }

        return $this->renderResponse($blockContext->getTemplate(), [
            'comments'  => $results,
            'book'      => $book,
            'block'     => $block,
            'settings'  => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}
