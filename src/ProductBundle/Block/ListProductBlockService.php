<?php

namespace ProductBundle\Block;

use BookBundle\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ListProductBlockService
 */
class ListProductBlockService extends AbstractAdminBlockService
{
    const POPULAR_LIST = 'BookBundle:Block:popular_list.html.twig';
    const TOP_100_LIST = 'BookBundle:Block:top_100_list.html.twig';

    /**
     * @var Registry $doctrine
     */
    protected $doctrine;

    /**
     * ListGenreBlockService constructor.
     *
     * @param string          $name
     * @param EngineInterface $templating
     * @param Registry        $doctrine
     */
    public function __construct($name, EngineInterface $templating, Registry $doctrine)
    {
        parent::__construct($name, $templating);

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
            'list_type'        => null,
            'items_count'      => 20,
            'popular'          => false,
            'popular_days_ago' => 30,
            'page'             => 1,
            'genre'            => null,
            'author'           => null,
            'series'           => null,
            'year'             => null,
            'tag'              => null,
            'top_book'         => false,
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

        $limit = (int) $blockContext->getSetting('items_count');
        $page = (int) $blockContext->getSetting('page');

        $repository = $this->doctrine->getRepository(Book::class);

        $qb = $repository->baseBookQueryBuilder();

        $popularDaysAgo = $blockContext->getSetting('popular_days_ago');
        if ($blockContext->getSetting('popular') && $popularDaysAgo) {
            $repository->filterPopularByDaysAgo($qb, (int) $popularDaysAgo);
        }

        if ($blockContext->getSetting('genre')) {
            $repository->filterByGenre($qb, $blockContext->getSetting('genre'));
        }

        if ($blockContext->getSetting('author')) {
            $repository->filterByAuthor($qb, $blockContext->getSetting('author'));
        }

        if ($blockContext->getSetting('series')) {
            $repository->filterBySeries($qb, $blockContext->getSetting('series'));
        }

        if ($blockContext->getSetting('top_book')) {
            $repository->filterByTop($qb);
        }

        if ($blockContext->getSetting('year')) {
            $repository->filterByYear($qb, $blockContext->getSetting('year'));
        }

        if ($blockContext->getSetting('tag')) {
            $repository->filterByTag($qb, $blockContext->getSetting('tag'));
        }

        $paginator = new Pagerfanta(new DoctrineORMAdapter($qb, true, false));
        $paginator->setAllowOutOfRangePages(true);
        $paginator->setMaxPerPage($limit);
        $paginator->setCurrentPage($page);

        $template = !is_null($blockContext->getSetting('list_type'))
            ? $blockContext->getSetting('list_type') : $blockContext->getTemplate();

        return $this->renderResponse($template, [
            'books'     => $paginator,
            'block'     => $block,
            'settings'  => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}
