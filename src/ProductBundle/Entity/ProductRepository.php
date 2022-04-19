<?php

namespace ProductBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use ShareBundle\Entity\Tag;

/**
 * Class ProductRepository
 */
class ProductRepository extends EntityRepository
{
    /**
     * @return QueryBuilder
     */
    public function baseProductQueryBuilder(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->where('p.isActive = 1')
            ->andWhere('p.isMainProduct = 1')
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

    /**
     * @param QueryBuilder $qb
     * @param Category|int $category
     *
     * @return QueryBuilder
     */
    public function filterByCategory(QueryBuilder $qb, $category) : QueryBuilder
    {
        return $qb->andWhere('p.category = :category')->setParameter('category', $category);
    }

    /**
     * @param  QueryBuilder  $qb
     * @param $who
     *
     * @return QueryBuilder
     */
    public function filterByWho(QueryBuilder $qb, $who) : QueryBuilder
    {
        if ($who === Product::WHO_MAN) {
            $qb->andWhere('p.isMan = :isMan')->setParameter('isMan', true);
        } elseif ($who === Product::WHO_WOMAN) {
            $qb->andWhere('p.isWoman = :isWoman')->setParameter('isWoman', true);
        }

        return $qb;
    }

    public function filterByDiscount(QueryBuilder $qb) : QueryBuilder
    {
        return $qb->andWhere('p.discount != :discount')->setParameter('discount', 0);
    }

    public function filterByColour(QueryBuilder $qb, $colour) : QueryBuilder
    {
        return $qb->innerJoin('p.colours', 'colour', 'WITH', 'colour.id = :colour')
            ->setParameter('colour', $colour);
    }

    public function filterByColours(QueryBuilder $qb, $colourIds) : QueryBuilder
    {
        return $qb->innerJoin('p.colours', 'colour', 'WITH', 'colour.id IN (:colour)')
            ->setParameter('colour', $colourIds);
    }

    public function filterByStone(QueryBuilder $qb, $stone) : QueryBuilder
    {
        return $qb->innerJoin('p.stones', 'stone', 'WITH', 'stone.id = :stone')
            ->setParameter('stone', $stone);
    }

    public function filterByStones(QueryBuilder $qb, $stoneIds) : QueryBuilder
    {
        return $qb->innerJoin('p.stones', 'stone', 'WITH', 'stone.id IN (:stone)')
            ->setParameter('stone', $stoneIds);
    }

    /**
     * @param QueryBuilder $qb
     * @param Tag|int      $tag
     *
     * @return QueryBuilder
     */
    public function filterByTag(QueryBuilder $qb, $tag) : QueryBuilder
    {
        return $qb->innerJoin('p.tags', 'tag', 'WITH', 'tag.id = :tag')
            ->setParameter('tag', $tag);
    }

    /**
     * @param  QueryBuilder  $qb
     * @param $excludeIds
     *
     * @return QueryBuilder
     */
    public function filterExclude(QueryBuilder $qb, $excludeIds) : QueryBuilder
    {
        return $qb->andWhere('p.id not in (:exclude_ids)')->setParameter('exclude_ids', $excludeIds);
    }

    /**
     * @param array $tags
     * @param array $excludeIds
     * @param int   $limit
     *
     * @return mixed
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

    /**
     * @param int $item
     */
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
