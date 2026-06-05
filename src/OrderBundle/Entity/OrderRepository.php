<?php

namespace OrderBundle\Entity;

use Doctrine\ORM\EntityRepository;

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
}