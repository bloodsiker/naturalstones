<?php

namespace ShareBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class StoneRepository
 */
class StoneRepository extends EntityRepository
{
    /**
     * @return mixed
     */
    public function baseStoneQueryBuilder()
    {
        $qb = $this->createQueryBuilder('s');
        $qb
            ->where('s.isActive = 1')
            ->orderBy('s.id', 'DESC');

        return $qb;
    }

    public function filterByShowMain(QueryBuilder $qb) : QueryBuilder
    {
        return $qb->andWhere('s.isShowMain = :isShowMain')->setParameter('isShowMain', true);
    }
}
