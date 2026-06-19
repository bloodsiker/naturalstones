<?php

namespace OrderBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PromoCodeRepository extends EntityRepository
{
    public function findValidByCode(string $code): ?PromoCode
    {
        return $this->createQueryBuilder('pc')
            ->where('pc.code = :code')
            ->andWhere('pc.isActive = true')
            ->andWhere('pc.expiresAt IS NULL OR pc.expiresAt > :now')
            ->andWhere('pc.usageLimit IS NULL OR pc.usageCount < pc.usageLimit')
            ->setParameter('code', strtoupper(trim($code)))
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getOneOrNullResult();
    }
}