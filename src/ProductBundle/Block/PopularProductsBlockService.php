<?php

namespace ProductBundle\Block;

use BookBundle\Entity\BookInfoView;
use Doctrine\Bundle\DoctrineBundle\Registry;
use ProductBundle\Entity\ProductInfoView;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PopularProductsBlockService
 */
class PopularProductsBlockService extends AbstractBlockService
{
    const POPULAR_LIST = 'ProductBundle:Block:popular_list.html.twig';

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
            'list_type'        => null,
            'items_count'      => 20,
            'popular_days_ago' => 0,
            'top_book'         => false,
            'by_month'         => false,
            'title'            => null,
            'template'         => self::POPULAR_LIST,
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

        $repository = $this->doctrine->getRepository(ProductInfoView::class);

        $qb = $repository->baseProductInfoViewQueryBuilder();

        if ($blockContext->getSetting('popular_days_ago')) {
            $repository->filterPopularByDaysAgo($qb, (int) $blockContext->getSetting('popular_days_ago'));
        }

        if ($blockContext->getSetting('by_month')) {
            $repository->filterPopularByMonth($qb, (int) 0);
        }

        $result = $qb->setFirstResult(0)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $template = !is_null($blockContext->getSetting('list_type'))
            ? $blockContext->getSetting('list_type') : $blockContext->getTemplate();

        return $this->renderResponse($template, [
            'products'  => $result,
            'block'     => $block,
            'settings'  => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}
