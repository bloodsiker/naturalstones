<?php

namespace PageBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class SiteVariableRepository
 */
class SiteVariableRepository extends EntityRepository
{
    /**
     * Returns SiteVariables. Items without Site comes first ("default" variables).
     *
     * @param string $placement
     *
     * @return array
     */
    public function findVariables($placement)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $expr = $qb->expr();

        $where = $expr->andX(
            $expr->eq('p.alias', ':placement'),
            $expr->eq('v.isActive', true)
        );

        $query = $qb
            ->select('v')
            ->from('PageBundle:SiteVariable', 'v')
            ->innerJoin('v.placement', 'p')

            ->where($where)
            ->setParameter(':placement', $placement)

            ->getQuery();

        return $query->getResult();
    }
}
