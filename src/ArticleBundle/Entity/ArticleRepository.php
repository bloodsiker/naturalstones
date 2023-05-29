<?php

namespace ArticleBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use ShareBundle\Entity\Tag;

/**
 * Class ArticleRepository
 */
class ArticleRepository extends EntityRepository
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

    /**
     * @return QueryBuilder
     */
    public function baseArticleQueryBuilder(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->where('a.isActive = 1')
            ->leftJoin('a.translations', 'at')
            ->addSelect('at')
            ->orderBy('a.id', 'DESC')
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
     */
    public function filterByLocale(QueryBuilder $qb, $search): QueryBuilder
    {
        $qb->andWhere('pt.name LIKE :search OR pt.description LIKE :search')
            ->setParameter('search', '%'.$search.'%');

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param Category|int $category
     *
     * @return QueryBuilder
     */
    public function filterByCategory(QueryBuilder $qb, $category): QueryBuilder
    {
        return $qb->andWhere('a.category = :category')->setParameter('category', $category);
    }

    /**
     * @param QueryBuilder $qb
     * @param Tag|int      $tag
     *
     * @return QueryBuilder
     */
    public function filterByTag(QueryBuilder $qb, $tag) : QueryBuilder
    {
        return $qb->innerJoin('a.tags', 'tag', 'WITH', 'tag.id = :tag')
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
        return $qb->andWhere('a.id not in (:exclude_ids)')->setParameter('exclude_ids', $excludeIds);
    }

    /**
     * @param int $item
     */
    public function incViewCounter(int $item): void
    {
        $qb = $this->createQueryBuilder('a');

        $qb
            ->update()
            ->set('a.views', 'a.views + 1')
            ->where('a.id = :item')
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
