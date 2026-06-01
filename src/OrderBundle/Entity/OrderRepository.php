<?php

namespace OrderBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class OrderRepository
 */
class OrderRepository extends EntityRepository
{
    public function claimSpin(Order $order): bool
    {
        $updatedRows = $this->createQueryBuilder('o')
            ->update()
            ->set('o.isSpin', ':isSpin')
            ->where('o.id = :id')
            ->andWhere('o.isSpin = :notSpun')
            ->setParameter('isSpin', true)
            ->setParameter('notSpun', false)
            ->setParameter('id', $order->getId())
            ->getQuery()
            ->execute();

        return 1 === $updatedRows;
    }

    /**
     * @return QueryBuilder
     */
    public function baseOrderBoardQueryBuilder(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('o');
        $qb
            ->orderBy('o.createdAt', 'DESC')
        ;

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $status
     *
     * @return QueryBuilder
     */
    public function filterByStatus(QueryBuilder $qb, string $status): QueryBuilder
    {
        if (in_array($status, OrderBoard::getStatuses())) {
            $statuses = array_flip(OrderBoard::getStatuses());
            $status = $statuses[$status];
            $qb->where('o.status = :status')->setParameter('status', $status);
        } else {
            $qb
                ->where('o.status NOT IN (:statuses)')
                    ->setParameter('statuses', [OrderBoard::STATUS_COMPLETED, OrderBoard::STATUS_CANCEL])
                ->resetDQLPart('orderBy')->orderBy('o.vote', 'DESC');
        }

        return $qb;
    }
}
