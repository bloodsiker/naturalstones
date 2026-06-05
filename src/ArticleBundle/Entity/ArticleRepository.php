<?php

namespace ArticleBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use ShareBundle\Entity\Tag;

class ArticleRepository extends EntityRepository
{
    public function baseArticleQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->where('a.isActive = 1')
            ->leftJoin('a.translations', 'at')
            ->addSelect('at')
            ->orderBy('a.id', 'DESC');
    }

    public function filterByCategory(QueryBuilder $qb, Category|int $category): QueryBuilder
    {
        return $qb->andWhere('a.category = :category')->setParameter('category', $category);
    }

    public function filterByTag(QueryBuilder $qb, Tag|int $tag): QueryBuilder
    {
        return $qb->innerJoin('a.tags', 'tag', 'WITH', 'tag.id = :tag')
            ->setParameter('tag', $tag);
    }

    public function incViewCounter(int $item): void
    {
        $this->createQueryBuilder('a')
            ->update()
            ->set('a.views', 'a.views + 1')
            ->where('a.id = :item')
            ->setParameter('item', $item)
            ->getQuery()
            ->execute();
    }
}