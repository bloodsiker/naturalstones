<?php

namespace BookBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use GenreBundle\Entity\Genre;
use SeriesBundle\Entity\Series;
use ShareBundle\Entity\Author;
use ShareBundle\Entity\Tag;

/**
 * Class BookRepository
 */
class BookRepository extends EntityRepository
{
    /**
     * @return QueryBuilder
     */
    public function baseBookQueryBuilder(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('b');
        $qb
            ->where('b.isActive = 1')
            ->orderBy('b.id', 'DESC')
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
        $genreIds = [$genre->getId()];
        if ($genre->getChildren()->count()) {
            foreach ($genre->getChildren()->getValues() as $child) {
                if ($child->getIsActive()) {
                    $genreIds[] = $child->getId();
                }
            }
        }

        return $qb->innerJoin('b.genres', 'genre', 'WITH', 'genre.id IN (:genre)')
            ->setParameter('genre', $genreIds);
    }

    /**
     * @param QueryBuilder $qb
     * @param Author       $author
     *
     * @return QueryBuilder
     */
    public function filterByAuthor(QueryBuilder $qb, Author $author) : QueryBuilder
    {
        return $qb->innerJoin('b.authors', 'author', 'WITH', 'author.id = :author')
            ->setParameter('author', $author);
    }

    /**
     * @param QueryBuilder $qb
     * @param Series       $series
     *
     * @return QueryBuilder
     */
    public function filterBySeries(QueryBuilder $qb, Series $series, $childBooks = false) : QueryBuilder
    {
        $qb->resetDQLPart('orderBy');

        if ($series->getType() === Series::TYPE_AUTHOR) {
            $qb
                ->andWhere('b.series = :series');
        } elseif ($series->getType() === Series::TYPE_PUBLISHING) {
            $qb
                ->andWhere('b.seriesPublishing = :series');
        };

        $qb
            ->setParameter('series', $series)
            ->orderBy('b.seriesNumber');

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param int          $year
     *
     * @return QueryBuilder
     */
    public function filterByYear(QueryBuilder $qb, int $year) : QueryBuilder
    {
        return $qb->andWhere('b.year = :year')->setParameter('year', $year);
    }

    /**
     * @param QueryBuilder $qb
     *
     * @return QueryBuilder
     */
    public function filterByTop(QueryBuilder $qb) : QueryBuilder
    {
        return $qb->resetDQLPart('orderBy')->orderBy('b.ratePlus - b.rateMinus', 'DESC');
    }

    /**
     * @param QueryBuilder $qb
     * @param Tag          $tag
     *
     * @return QueryBuilder
     */
    public function filterByTag(QueryBuilder $qb, Tag $tag) : QueryBuilder
    {
        return $qb->innerJoin('b.tags', 'tag', 'WITH', 'tag.id = :tag')
            ->setParameter('tag', $tag);
    }

    /**
     * @param QueryBuilder $qb
     * @param int          $period
     *
     * @return QueryBuilder
     *
     * @throws \Exception
     */
    public function filterPopularByDaysAgo(QueryBuilder $qb, int $period): QueryBuilder
    {
        $timeAgo = $this->getNow()->modify(" -{$period} day");

        $qb
            ->andWhere('b.createdAt > :timeAgo')
            ->setParameter('timeAgo', $timeAgo);

        $this->filterByTop($qb);

        return $qb;
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
        $qb = $this->baseBookQueryBuilder();
        $qb
            ->innerJoin('b.tags', 'tag', 'WITH', 'tag.id IN (:tags)')
            ->setParameter('tags', $tags)
            ->setFirstResult(0)
            ->setMaxResults($limit)
        ;

        if ($excludeIds) {
            $qb->andWhere('b.id not in (:exclude_ids)')->setParameter('exclude_ids', $excludeIds);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int $item
     */
    public function incViewCounter(int $item): void
    {
        $qb = $this->createQueryBuilder('b');

        $qb
            ->update()
            ->set('b.views', 'b.views + 1')
            ->where('b.id = :item')
            ->setParameter(':item', $item)
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * @param int $item
     */
    public function incDownloadCounter(int $item): void
    {
        $qb = $this->createQueryBuilder('b');

        $qb
            ->update()
            ->set('b.download', 'b.download + 1')
            ->where('b.id = :item')
            ->setParameter(':item', $item)
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * @return mixed
     */
    public function getUniqueYear()
    {
        $qb = $this->baseBookQueryBuilder();

        return $qb
            ->where('b.year IS NOT NULL')
            ->groupBy('b.year')
            ->resetDQLPart('orderBy')
            ->orderBy('b.seriesNumber')
            ->getQuery()
            ->getResult();
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
