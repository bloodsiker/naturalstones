<?php

namespace BookBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use GenreBundle\Entity\Genre;

/**
 * Class BookCollectionRepository
 */
class BookCollectionRepository extends EntityRepository
{
    /**
     * @return QueryBuilder
     */
    public function baseCollectionQueryBuilder(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('bc');
        $qb
            ->where('bc.isActive = 1')
            ->orderBy('bc.id', 'DESC')
        ;

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param Genre        $genre
     *
     * @return QueryBuilder
     */
    public function filterByGenre(QueryBuilder $qb, Genre $genre): QueryBuilder
    {
        return $qb->innerJoin('bc.genres', 'genre', 'WITH', 'genre.id = :genre')
            ->setParameter('genre', $genre);
    }

    /**
     * @return QueryBuilder
     */
    public function getGenresCollection()
    {
        $sql = 'SELECT 
                    g.*
                FROM genres g
                INNER JOIN books_collection_genres bcg
                    ON bcg.genre_id = g.id
                WHERE g.is_active = 1
                GROUP BY g.id';

        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(Genre::class, 'g');
        $rsm->addFieldResult('g', 'id', 'id');
        $rsm->addFieldResult('g', 'name', 'name');
        $rsm->addFieldResult('g', 'slug', 'slug');
        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);

        return $query->getResult();
    }

    /**
     * @param int $item
     */
    public function incViewCounter(int $item): void
    {
        $qb = $this->createQueryBuilder('bc');

        $qb
            ->update()
            ->set('bc.views', 'bc.views + 1')
            ->where('bc.id = :item')
            ->setParameter(':item', $item)
            ->getQuery()
            ->execute()
        ;
    }
}
