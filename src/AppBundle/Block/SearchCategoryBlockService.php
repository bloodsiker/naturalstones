<?php

namespace AppBundle\Block;

use Doctrine\ORM\EntityManagerInterface;
use ProductBundle\Entity\Product;
use ProductBundle\Entity\ProductRepository;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class SearchCategoryBlockService extends AbstractEditableBlockService
{
    use SearchHistoryTrait;

    public const DEFAULT_TEMPLATE = '@App/search_category/Block/large_list.html.twig';

    public function __construct(
        Environment $twig,
        protected readonly EntityManagerInterface $em,
        private readonly RequestStack $request,
    ) {
        parent::__construct($twig);
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'list_type' => null,
            'search' => null,
            'items_count' => 40,
            'template' => self::DEFAULT_TEMPLATE,
        ]);
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $block = $blockContext->getBlock();
        if (!$block->getEnabled()) {
            return new Response();
        }

        $request = $this->request->getCurrentRequest();
        $search = trim((string) ($blockContext->getSetting('search') ?: $request->get('search')));
        $resultDetails = [];

        if ($search) {
            /** @var ProductRepository $repository */
            $repository = $this->em->getRepository(Product::class);

            foreach ($this->getMatchedCategoryStats($repository, $search) as $category) {
                $products = $this->getCategoryProducts($repository, $search, $category['categoryId'], 4);

                if (!$products) {
                    continue;
                }

                $resultDetails[] = [
                    'sort' => $category['sort'],
                    'category' => $products[0]->getCategory(),
                    'count' => (int) $category['productCount'],
                    'products' => $products,
                ];
            }

            $this->saveSearchHistory($search, $request->server->get('REMOTE_ADDR'));
        }

        $template = $blockContext->getSetting('list_type') ?? $blockContext->getTemplate();

        return $this->renderResponse($template, [
            'result' => $resultDetails,
            'search' => $search,
            'block' => $block,
            'settings' => array_merge($blockContext->getSettings(), $block->getSettings()),
        ]);
    }

    /**
     * @return array<int, array{categoryId: int, productCount: int, sort: int}>
     */
    private function getMatchedCategoryStats(ProductRepository $repository, string $search): array
    {
        $qb = $repository->createQueryBuilder('p');
        $qb
            ->select('IDENTITY(p.category) AS categoryId')
            ->addSelect('COUNT(DISTINCT p.id) AS productCount')
            ->addSelect('c.orderNum AS sort')
            ->innerJoin('p.category', 'c')
            ->leftJoin('p.translations', 'pt')
            ->where('p.isActive = 1')
            ->andWhere('p.isMainProduct = 1')
            ->groupBy('c.id')
            ->addGroupBy('c.orderNum')
            ->orderBy('c.orderNum', 'DESC');

        $repository->filterByLocale($qb, $search);

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * @return list<Product>
     */
    private function getCategoryProducts(ProductRepository $repository, string $search, int $categoryId, int $limit): array
    {
        $idsQb = $repository->createQueryBuilder('p');
        $idsQb
            ->select('DISTINCT p.id')
            ->leftJoin('p.translations', 'pt')
            ->where('p.isActive = 1')
            ->andWhere('p.isMainProduct = 1')
            ->andWhere('IDENTITY(p.category) = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->orderBy('p.views', 'DESC')
            ->setMaxResults($limit);

        $repository->filterByLocale($idsQb, $search);

        $ids = array_map('current', $idsQb->getQuery()->getScalarResult());

        if (!$ids) {
            return [];
        }

        $qb = $repository->baseProductQueryBuilder();
        $qb
            ->innerJoin('p.category', 'category')
            ->addSelect('category')
            ->andWhere('p.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->orderBy('p.views', 'DESC');

        return $qb->getQuery()->getResult();
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }
}
