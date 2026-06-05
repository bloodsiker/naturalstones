<?php

namespace WheelSpinBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class WheelSpinRepository extends EntityRepository
{
    public function getWheelSpin(float|int|string $sum): ?WheelSpin
    {
        $sum = round((float) $sum, 2);

        return $this->findWithinRange($sum)
            ?? $this->findAboveMin($sum)
            ?? $this->findFallback();
    }

    private function findWithinRange(float $sum): ?WheelSpin
    {
        return $this->baseActiveQueryBuilder()
            ->andWhere('ws.minSum <= :sum')
            ->andWhere('ws.maxSum >= :sum')
            ->setParameter('sum', $sum)
            ->orderBy('ws.minSum', 'DESC')
            ->addOrderBy('ws.maxSum', 'ASC')
            ->addOrderBy('ws.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function findAboveMin(float $sum): ?WheelSpin
    {
        return $this->baseActiveQueryBuilder()
            ->andWhere('ws.minSum <= :sum')
            ->setParameter('sum', $sum)
            ->orderBy('ws.minSum', 'DESC')
            ->addOrderBy('ws.maxSum', 'ASC')
            ->addOrderBy('ws.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function findFallback(): ?WheelSpin
    {
        return $this->baseActiveQueryBuilder()
            ->orderBy('ws.minSum', 'ASC')
            ->addOrderBy('ws.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function baseActiveQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('ws')->where('ws.isActive = 1');
    }
}