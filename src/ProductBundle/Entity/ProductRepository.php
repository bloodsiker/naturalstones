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
            ->orderBy('p.id', 'DESC')
        ;

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param Category     $category
     *
     * @return QueryBuilder
     */
    public function filterByCategory(QueryBuilder $qb, Category $category) : QueryBuilder
    {
        return $qb->andWhere('p.category = :category')->setParameter('category', $category);
    }

    /**
     * @param QueryBuilder $qb
     * @param Tag          $tag
     *
     * @return QueryBuilder
     */
    public function filterByTag(QueryBuilder $qb, Tag $tag) : QueryBuilder
    {
        return $qb->innerJoin('p.tags', 'tag', 'WITH', 'tag.id = :tag')
            ->setParameter('tag', $tag);
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
