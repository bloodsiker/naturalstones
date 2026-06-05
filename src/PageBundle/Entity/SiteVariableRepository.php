<?php

namespace PageBundle\Entity;

use Doctrine\ORM\EntityRepository;

class SiteVariableRepository extends EntityRepository
{
    /**
     * Returns SiteVariables. Items without Site come first ("default" variables).
     *
     * @return list<SiteVariable>
     */
    public function findVariables(?string $placement): array
    {
        return $this->createQueryBuilder('v')
            ->select('v')
            ->innerJoin('v.placement', 'p')
            ->where('p.alias = :placement')
            ->andWhere('v.isActive = true')
            ->setParameter('placement', $placement)
            ->getQuery()
            ->getResult();
    }
}