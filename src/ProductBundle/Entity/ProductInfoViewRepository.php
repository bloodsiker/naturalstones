<?php

namespace ProductBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class ProductInfoViewRepository
 */
class ProductInfoViewRepository extends EntityRepository
{
    /**
     * @return QueryBuilder
     */
    public function baseProductInfoViewQueryBuilder(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('piv');
        $qb
            ->where('piv.product IS NOT NULL')
            ->andWhere('product.isMainProduct = 1')
            ->innerJoin('piv.product', 'product', 'WITH', 'product.id = piv.product')
            ->addSelect('SUM(piv.views) AS HIDDEN sumView')
            ->groupBy('piv.product')
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
            ->andWhere('piv.viewAt > :timeAgo')
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
            ->andWhere('piv.viewAt BETWEEN :first AND :last')
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
