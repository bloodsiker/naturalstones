<?php

namespace ShareBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class ZodiacRepository extends EntityRepository
{
    public function baseStoneQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('z')
            ->where('z.isActive = 1')
            ->orderBy('z.id', 'DESC');
    }

    public function filterByShowMain(QueryBuilder $qb): QueryBuilder
    {
        return $qb->andWhere('z.isShowMain = :isShowMain')->setParameter('isShowMain', true);
    }
}