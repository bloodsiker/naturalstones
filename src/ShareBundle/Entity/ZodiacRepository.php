<?php

namespace ShareBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class ZodiacRepository
 */
class ZodiacRepository extends EntityRepository
{
    /**
     * @return mixed
     */
    public function baseStoneQueryBuilder()
    {
        $qb = $this->createQueryBuilder('z');
        $qb
            ->where('z.isActive = 1')
            ->orderBy('z.id', 'DESC');

        return $qb;
    }

    public function filterByShowMain(QueryBuilder $qb) : QueryBuilder
    {
        return $qb->andWhere('z.isShowMain = :isShowMain')->setParameter('isShowMain', true);
    }
}
