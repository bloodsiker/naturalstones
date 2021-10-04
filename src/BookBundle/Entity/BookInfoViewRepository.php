<?php

namespace BookBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use GenreBundle\Entity\Genre;
use SeriesBundle\Entity\Series;
use ShareBundle\Entity\Author;
use ShareBundle\Entity\Tag;

/**
 * Class BookInfoViewRepository
 */
class BookInfoViewRepository extends EntityRepository
{
    /**
     * @return QueryBuilder
     */
    public function baseBookInfoViewQueryBuilder(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('biv');
        $qb
            ->addSelect('SUM(biv.views) AS HIDDEN sumView')
            ->groupBy('biv.book')
            ->orderBy('sumView', 'DESC')
        ;

        return $qb;
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
            ->andWhere('biv.viewAt > :timeAgo')
            ->setParameter('timeAgo', $timeAgo);

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param int          $difference
     *
     * @return QueryBuilder
     *
     * @throws \Exception
     */
    public function filterPopularByMonth(QueryBuilder $qb, int $difference = 0): QueryBuilder
    {
        $firstDay = (new \DateTime("first day of -$difference month midnight"))->format('Y-m-d');
        $lastDay = (new \DateTime("last day of -$difference month midnight"))->format('Y-m-d');

        $qb
            ->andWhere('biv.viewAt BETWEEN :first AND :last')
            ->setParameter('first', $firstDay)
            ->setParameter('last', $lastDay);

        return $qb;
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
