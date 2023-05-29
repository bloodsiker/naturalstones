<?php

namespace ProductBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class CategoryRepository
 */
class CategoryRepository extends EntityRepository
{
    /**
     * @return QueryBuilder
     */
    public function baseCategoryQueryBuilder(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('c');
        $qb
            ->leftJoin('c.translations', 'ct')
            ->addSelect('ct')
            ->where('c.isActive = 1')
            ->orderBy('c.id', 'DESC');

        return $qb;
    }
}
