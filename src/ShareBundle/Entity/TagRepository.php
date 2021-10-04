<?php

namespace ShareBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class TagRepository
 */
class TagRepository extends EntityRepository
{
    /**
     * @param array $excludeIds
     *
     * @return mixed
     */
    public function baseTagQueryBuilder(array $excludeIds = [])
    {
        $qb = $this->createQueryBuilder('t');
        $qb
            ->select('t.id', 't.name')
            ->where('t.isActive = 1')
            ->orderBy('t.id', 'DESC')
        ;

        if ($excludeIds) {
            $qb->andWhere('t.id not in (:exclude_ids)')->setParameter('exclude_ids', $excludeIds);
        }

        return $qb->getQuery()->getResult();
    }
}
