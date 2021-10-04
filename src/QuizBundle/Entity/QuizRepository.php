<?php

namespace QuizBundle\Entity;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;

/**
 * Class QuizRepository
 */
class QuizRepository extends EntityRepository
{

    /**
     * Quiz list query builder for Pagerfanta paginator
     *
     * @return QueryBuilder
     *
     * @throws \Exception
     */
    public function createBaseQuizList()
    {
        $qb = $this
            ->createQueryBuilder('q')
            ->andWhere('q.isActive = 1')
            ->orderBy('q.createdAt', 'DESC')
        ;

        return $qb;
    }
}