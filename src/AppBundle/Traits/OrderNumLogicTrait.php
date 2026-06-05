<?php

namespace AppBundle\Traits;

use Doctrine\ORM\NoResultException;

/**
 * Provides ordering helpers for repositories whose entity has an `orderNum` property.
 *
 * Hosting class is expected to extend Doctrine\ORM\EntityRepository.
 */
trait OrderNumLogicTrait
{
    private string $reorderQuery = 'set @i:= -99;
        UPDATE %s
        SET order_num = ( @i := @i + 100 )
        order by order_num';

    public function getHighestPropertyByOrder(string $property, string $order = 'DESC'): int|float|string
    {
        $qb = $this->createQueryBuilder('object')
            ->select('object.' . $property)
            ->orderBy('object.' . $property, $order)
            ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException) {
            return 0;
        }
    }

    public function getHighestPropertyByPosition(string $property, object $except, string $order = 'DESC'): ?object
    {
        $comparison = 'DESC' !== $order ? '>=' : '<=';

        return $this->createQueryBuilder('object')
            ->select('object')
            ->andWhere(sprintf('object.%s %s :property', $property, $comparison))
            ->setParameter('property', $except->getOrderNum())
            ->andWhere('object.id != :except')
            ->setParameter('except', $except)
            ->orderBy('object.' . $property, $order)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function reCountOrderNum(): void
    {
        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare(
            sprintf($this->reorderQuery, $this->getClassMetadata()->getTableName())
        );
        $stmt->execute();
    }
}