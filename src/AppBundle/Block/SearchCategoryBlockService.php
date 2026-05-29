<?php

namespace AppBundle\Block;

use Doctrine\ORM\EntityManager;
use ProductBundle\Entity\Product;
use ProductBundle\Entity\ProductSearchHistory;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

/**
 * Class SearchCategoryBlockService
 */
class SearchCategoryBlockService extends AbstractBlockService
{
    const DEFAULT_TEMPLATE = 'AppBundle:search_category/Block:large_list.html.twig';

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * HeaderBlockService constructor.
     *
     * @param string          $name
     * @param EngineInterface $templating
     * @param EntityManager   $em
     * @param RequestStack    $request
     */
    public function __construct(Environment $twig, EntityManager $em, RequestStack $request)
    {
        parent::__construct($twig);

        $this->em = $em;
        $this->request = $request;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver): void    {
        $resolver->setDefaults([
            'list_type'      => null,
            'search'         => null,
            'items_count'    => 40,
            'template'       => self::DEFAULT_TEMPLATE,
        ]);
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null         $response
     *
     * @return Response
     */
    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response    {
        $block = $blockContext->getBlock();
        if (!$block->getEnabled()) {
            return new Response();
        }

        $request = $this->request->getCurrentRequest();

        $search = trim((string) ($blockContext->getSetting('search') ?: $request->get('search')));
        $resultDetails = [];

        if ($search) {
            $repository = $this->em->getRepository(Product::class);

            $categories = $this->getMatchedCategoryStats($repository, $search);

            foreach ($categories as $category) {
                $products = $this->getCategoryProducts($repository, $search, $category['categoryId'], 4);

                if (!$products) {
                    continue;
                }

                $resultDetails[] = [
                    'sort'     => $category['sort'],
                    'category' => $products[0]->getCategory(),
                    'count'    => (int) $category['productCount'],
                    'products' => $products,
                ];
            }

            $this->saveSearchHistory($search, $request->server->get('REMOTE_ADDR'));
        }

        $template = !is_null($blockContext->getSetting('list_type'))
            ? $blockContext->getSetting('list_type') : $blockContext->getTemplate();

        return $this->renderResponse($template, [
            'result'      => $resultDetails,
            'search'      => $search,
            'block'       => $block,
            'settings'    => array_merge($blockContext->getSettings(), $block->getSettings()),
        ]);
    }

    private function getMatchedCategoryStats($repository, $search)
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

    private function getCategoryProducts($repository, $search, $categoryId, $limit)
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

    private function saveSearchHistory($search, $ip)
    {
        if (strlen($search) < 2) {
            return;
        }

        $ip = $ip ? substr($ip, 0, 20) : null;

        $qb = $this->em->getRepository(ProductSearchHistory::class)
            ->createQueryBuilder('history')
            ->where('history.search = :search')
            ->andWhere('history.createdAt >= :createdAt')
            ->setParameter('search', $search)
            ->setParameter('createdAt', new \DateTime('-15 minutes'))
            ->setMaxResults(1);

        if ($ip) {
            $qb->andWhere('history.ip = :ip')->setParameter('ip', $ip);
        } else {
            $qb->andWhere('history.ip IS NULL');
        }

        $recentSearch = $qb->getQuery()->getOneOrNullResult();

        if ($recentSearch) {
            return;
        }

        $history = new ProductSearchHistory();
        $history->setSearch($search);
        $history->setIp($ip);

        $this->em->persist($history);
        $this->em->flush();
    }
}
