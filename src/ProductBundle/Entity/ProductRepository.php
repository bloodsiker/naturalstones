<?php

namespace ProductBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use ShareBundle\Entity\Colour;
use ShareBundle\Entity\Stone;
use ShareBundle\Entity\Tag;

/**
 * Class ProductRepository
 */
class ProductRepository extends EntityRepository
{
    /**
     * @var string
     */
    private $locale;

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function baseProductQueryBuilder(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->where('p.isActive = 1')
            ->andWhere('p.isMainProduct = 1')
            ->leftJoin('p.translations', 'pt')
            ->addSelect('pt')
            ->orderBy('p.orderNum', 'DESC')
            ->addOrderBy('p.id', 'DESC')
        ;

        return $qb;
    }

    public function productGroupQueryBuilder($groupId): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->where('p.isActive = 1')
            ->andWhere('p.size IS NOT NULL')
            ->innerJoin('p.size', 'size', 'WITH', 'size.id = p.size')
            ->andWhere('p.productGroup IS NOT NULL')
            ->andWhere('p.productGroup = :groupId')->setParameter('groupId', $groupId)
            ->orderBy('size.name', 'DESC')
        ;

        return $qb;
    }

    public function filterByLocale(QueryBuilder $qb, $search): QueryBuilder
    {
        $qb->andWhere('pt.name LIKE :search OR pt.description LIKE :search')
            ->setParameter('search', '%' . $search . '%');

        return $qb;
    }

    /**
     * @param Category|int $category
     */
    public function filterByCategory(QueryBuilder $qb, $category): QueryBuilder
    {
        return $qb->andWhere('p.category = :category')->setParameter('category', $category);
    }

    public function filterByWho(QueryBuilder $qb, $who): QueryBuilder
    {
        if ($who === Product::WHO_MAN) {
            $qb->andWhere('p.isMan = :isMan')->setParameter('isMan', true);
        } elseif ($who === Product::WHO_WOMAN) {
            $qb->andWhere('p.isWoman = :isWoman')->setParameter('isWoman', true);
        }

        return $qb;
    }

    public function filterByDiscount(QueryBuilder $qb): QueryBuilder
    {
        return $qb->andWhere('p.discount != :discount')->setParameter('discount', 0);
    }

    public function filterByRand(QueryBuilder $qb): QueryBuilder
    {
        return $qb->addSelect('RAND() as HIDDEN rand')->orderBy('RAND()');
    }

    public function filterByColour(QueryBuilder $qb, $colour): QueryBuilder
    {
        return $qb->innerJoin('p.colours', 'colour', 'WITH', 'colour.id = :colour')
            ->setParameter('colour', $colour);
    }

    public function filterByColours(QueryBuilder $qb, $colourIds): QueryBuilder
    {
        return $qb->innerJoin('p.colours', 'colour', 'WITH', 'colour.id IN (:colour)')
            ->setParameter('colour', $colourIds);
    }

    public function filterByStone(QueryBuilder $qb, $stone): QueryBuilder
    {
        return $qb->innerJoin('p.stones', 'stone', 'WITH', 'stone.id = :stone')
            ->setParameter('stone', $stone);
    }

    public function filterByStones(QueryBuilder $qb, $stoneIds): QueryBuilder
    {
        return $qb->innerJoin('p.stones', 'stone', 'WITH', 'stone.id IN (:stone)')
            ->setParameter('stone', $stoneIds);
    }

    /**
     * @param Tag|int      $tag
     */
    public function filterByTag(QueryBuilder $qb, $tag): QueryBuilder
    {
        return $qb->innerJoin('p.tags', 'tag', 'WITH', 'tag.id = :tag')
            ->setParameter('tag', $tag);
    }

    public function filterByCategories(QueryBuilder $qb, $categoryIds): QueryBuilder
    {
        return $qb->andWhere('p.category IN (:categories)')->setParameter('categories', $categoryIds);
    }

    /**
     * @param int[] $tagIds
     * @return Product[]
     */
    public function findByTagIds(array $tagIds, int $limit = 4): array
    {
        if (empty($tagIds)) {
            return [];
        }

        $ids = $this->createQueryBuilder('p')
            ->select('p.id')
            ->distinct(true)
            ->innerJoin('p.tags', 'ptag')
            ->where('p.isActive = 1')
            ->andWhere('p.isMainProduct = 1')
            ->andWhere('ptag.id IN (:tagIds)')
            ->setParameter('tagIds', $tagIds)
            ->orderBy('p.orderNum', 'DESC')
            ->addOrderBy('p.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getSingleColumnResult();

        if (empty($ids)) {
            return [];
        }

        return $this->baseProductQueryBuilder()
            ->andWhere('p.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }

    public function filterExclude(QueryBuilder $qb, $excludeIds): QueryBuilder
    {
        return $qb->andWhere('p.id not in (:exclude_ids)')->setParameter('exclude_ids', $excludeIds);
    }

    /**
     * Min/max product price within the given scope, in a single aggregate query.
     *
     * @return array{minPrice: ?string, maxPrice: ?string}
     */
    public function getPriceBounds(QueryBuilder $scopeQb): array
    {
        return (clone $scopeQb)
            ->resetDQLPart('select')
            ->resetDQLPart('orderBy')
            ->select('MIN(p.price) AS minPrice, MAX(p.price) AS maxPrice')
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * Distinct stones available for the products in the given scope.
     *
     * @return Stone[]
     */
    public function findAvailableStones(QueryBuilder $scopeQb): array
    {
        $ids = $this->distinctRelationIds($scopeQb, 'p.stones');

        return $ids ? $this->getEntityManager()->getRepository(Stone::class)->findBy(['id' => $ids]) : [];
    }

    /**
     * Distinct colours available for the products in the given scope.
     *
     * @return Colour[]
     */
    public function findAvailableColours(QueryBuilder $scopeQb): array
    {
        $ids = $this->distinctRelationIds($scopeQb, 'p.colours');

        return $ids ? $this->getEntityManager()->getRepository(Colour::class)->findBy(['id' => $ids]) : [];
    }

    /**
     * Distinct categories available for the products in the given scope.
     *
     * @return Category[]
     */
    public function findAvailableCategories(QueryBuilder $scopeQb): array
    {
        $ids = $this->distinctRelationIds($scopeQb, 'p.category');

        return $ids
            ? $this->getEntityManager()->getRepository(Category::class)->findBy(['id' => $ids], ['orderNum' => 'DESC'])
            : [];
    }

    /**
     * Distinct related entity ids (e.g. stones, colours) for the products in the given scope.
     *
     * A joined entity cannot be selected directly through DQL while Product is the root,
     * so we collect the ids here and hydrate the entities separately.
     *
     * @return int[]
     */
    private function distinctRelationIds(QueryBuilder $scopeQb, string $relation): array
    {
        $rows = (clone $scopeQb)
            ->resetDQLPart('select')
            ->resetDQLPart('orderBy')
            ->select('rel.id')
            ->distinct()
            ->innerJoin($relation, 'rel')
            ->getQuery()
            ->getScalarResult();

        return array_map('intval', array_column($rows, 'id'));
    }

    /**
     * @param int   $limit
     *
     * @throws \Exception
     */
    public function getRelatedByTagsBooks(array $tags = [], array $excludeIds = [], $limit = 50)
    {
        $qb = $this->baseProductQueryBuilder();
        $qb
            ->innerJoin('p.tags', 'tag', 'WITH', 'tag.id IN (:tags)')
            ->setParameter('tags', $tags)
            ->setFirstResult(0)
            ->setMaxResults($limit)
        ;

        if ($excludeIds) {
            $qb->andWhere('p.id not in (:exclude_ids)')->setParameter('exclude_ids', $excludeIds);
        }

        return $qb->getQuery()->getResult();
    }

    public function incViewCounter(int $item): void
    {
        $qb = $this->createQueryBuilder('p');

        $qb
            ->update()
            ->set('p.views', 'p.views + 1')
            ->where('p.id = :item')
            ->setParameter(':item', $item)
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * Returns current date and time, rounded to nearest minute
     *
     * @return \DateTime
     *
     * @throws \Exception
     *
     * @todo move to helper class
     */
    public function getNow()
    {
        $now = new \DateTime('now');

        $now = new \DateTime($now->format('d-m-Y H:i:00'));

        return $now;
    }
}
