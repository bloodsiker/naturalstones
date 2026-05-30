<?php

namespace BookBundle\Block;

use BookBundle\Entity\BookCollection;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use AppBundle\Block\AbstractEditableBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ListCollectionBlockService
 */
class ListCollectionBlockService extends AbstractEditableBlockService
{
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
    public function __construct(Environment $twig, Registry $doctrine)
    {
        parent::__construct($twig);

        $this->doctrine = $doctrine;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver): void    {
        $resolver->setDefaults([
            'items_count'   => 20,
            'page'          => 1,
            'genre'         => null,
            'template'      => '@Book/Block/collection_list.html.twig',
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
    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response    {
        $block = $blockContext->getBlock();

        if (!$block->getEnabled()) {
            return new Response();
        }

        $limit = (int) $blockContext->getSetting('items_count');
        $page = (int) $blockContext->getSetting('page');

        $repository = $this->doctrine->getRepository(BookCollection::class);

        $qb = $repository->baseCollectionQueryBuilder();

        if ($blockContext->getSetting('genre')) {
            $repository->filterByGenre($qb, $blockContext->getSetting('genre'));
        }

        $paginator = new Pagerfanta(new QueryAdapter($qb, true, false));
        $paginator->setAllowOutOfRangePages(true);
        $paginator->setMaxPerPage($limit);
        $paginator->setCurrentPage($page);

        return $this->renderResponse($blockContext->getTemplate(), [
            'collections' => $paginator,
            'block'       => $block,
            'settings'    => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}
