<?php

namespace ProductBundle\Block;

use AppBundle\Block\AbstractEditableBlockService;
use Doctrine\Bundle\DoctrineBundle\Registry;
use ProductBundle\Entity\Product;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class ListViewedProductBlockService extends AbstractEditableBlockService
{
    public const TEMPLATE_DEFAULT = '@Product/Block/viewed_list.html.twig';
    public const TEMPLATE_AJAX = '@Product/Block/viewed_list_ajax.html.twig';

    private const DEFAULT_LOCALE = 'uk';

    public function __construct(
        Environment $twig,
        protected readonly Registry $doctrine,
        protected readonly RequestStack $requestStack,
    ) {
        parent::__construct($twig);
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'items_count' => 6,
            'class' => 'section',
            'template' => self::TEMPLATE_DEFAULT,
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

        $request = $this->requestStack->getCurrentRequest();
        $limit = (int) $blockContext->getSetting('items_count');
        $result = [];

        if ($request->isXmlHttpRequest()) {
            $ids = array_slice((array) $request->get('ids', []), 0, $limit);
            if ($ids) {
                $result = $this->loadProducts($ids, $request->getLocale(), $limit);
            }
        }

        return $this->renderResponse(
            $request->isXmlHttpRequest() ? self::TEMPLATE_AJAX : $blockContext->getTemplate(),
            [
                'products' => $result,
                'block' => $block,
                'settings' => array_merge($blockContext->getSettings(), $block->getSettings()),
            ],
            $response
        );
    }

    /**
     * @param list<int|string> $ids
     *
     * @return list<Product>
     */
    private function loadProducts(array $ids, ?string $locale, int $limit): array
    {
        $repository = $this->doctrine->getRepository(Product::class);
        $allProducts = $repository->baseProductQueryBuilder()
            ->where('p.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->resetDQLPart('orderBy')
            ->getQuery()
            ->getResult();

        $deduped = $this->dedupeByGroup($allProducts);
        $locale = $this->normalizeLocale($locale);

        foreach ($deduped as $product) {
            $product->setCurrentLocale($locale);
            if ($product->getCategory()) {
                $product->getCategory()->setCurrentLocale($locale);
            }
        }

        $orderMap = array_flip($ids);
        usort($deduped, fn (Product $a, Product $b) => ($orderMap[$a->getId()] ?? PHP_INT_MAX) <=> ($orderMap[$b->getId()] ?? PHP_INT_MAX));

        return array_slice($deduped, 0, $limit);
    }

    /**
     * @param list<Product> $products
     *
     * @return list<Product>
     */
    private function dedupeByGroup(array $products): array
    {
        $seen = [];
        $deduped = [];

        foreach ($products as $product) {
            $groupKey = $product->getProductGroup() ?? ('id_' . $product->getId());
            if (!isset($seen[$groupKey])) {
                $seen[$groupKey] = true;
                $deduped[] = $product;
            }
        }

        return $deduped;
    }

    private function normalizeLocale(?string $locale): string
    {
        return 'ua' === $locale ? 'uk' : ($locale ?: self::DEFAULT_LOCALE);
    }
}