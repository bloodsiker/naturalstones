<?php

namespace WheelSpinBundle\Entity;

use AppBundle\Traits\OrderNumLogicTrait;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class WheelSpinRepository
 */
class WheelSpinRepository extends EntityRepository
{
    public function getWheelSpin($sum)
    {
        $qb = $this->createQueryBuilder('ws');

        $wheelSpin = $qb
            ->where('ws.isActive = 1')
            ->andWhere('ws.minSum <= :sum')
            ->andWhere('ws.maxSum >= :sum')
            ->setParameter('sum', $sum)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($wheelSpin) {
            return $wheelSpin;
        }

        return $this->createQueryBuilder('ws')
            ->where('ws.isActive = 1')
            ->orderBy('ws.minSum', 'ASC')
            ->addOrderBy('ws.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
