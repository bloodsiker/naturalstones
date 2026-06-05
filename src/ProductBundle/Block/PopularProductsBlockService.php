<?php

namespace ProductBundle\Block;

use AppBundle\Block\AbstractEditableBlockService;
use Doctrine\Bundle\DoctrineBundle\Registry;
use ProductBundle\Entity\ProductInfoView;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class PopularProductsBlockService extends AbstractEditableBlockService
{
    public const POPULAR_LIST = '@Product/Block/popular_list.html.twig';

    public function __construct(
        Environment $twig,
        protected readonly Registry $doctrine,
    ) {
        parent::__construct($twig);
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'list_type' => null,
            'items_count' => 20,
            'popular_days_ago' => 0,
            'top_book' => false,
            'by_month' => false,
            'title' => null,
            'template' => self::POPULAR_LIST,
        ]);
    }

    /**
     * @throws \Exception
     */
    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $block = $blockContext->getBlock();
        if (!$block->getEnabled()) {
            return new Response();
        }

        $limit = (int) $blockContext->getSetting('items_count');
        $repository = $this->doctrine->getRepository(ProductInfoView::class);
        $qb = $repository->baseProductInfoViewQueryBuilder();

        if ($daysAgo = (int) $blockContext->getSetting('popular_days_ago')) {
            $repository->filterPopularByDaysAgo($qb, $daysAgo);
        }
        if ($blockContext->getSetting('by_month')) {
            $repository->filterPopularByMonth($qb, 0);
        }

        $result = $qb->setFirstResult(0)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $template = $blockContext->getSetting('list_type') ?? $blockContext->getTemplate();

        return $this->renderResponse($template, [
            'products' => $result,
            'block' => $block,
            'settings' => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}