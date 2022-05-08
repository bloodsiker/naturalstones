<?php

namespace ShareBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class StoneRepository
 */
class StoneRepository extends EntityRepository
{
    /**
     * @return mixed
     */
    public function baseStoneQueryBuilder()
    {
        $qb = $this->createQueryBuilder('s');
        $qb
            ->where('s.isActive = 1')
            ->orderBy('s.id', 'DESC');

        return $qb;
    }

    public function filterByShowMain(QueryBuilder $qb) : QueryBuilder
    {
        return $qb->andWhere('s.isShowMain = :isShowMain')->setParameter('isShowMain', true);
    }

    public function filterByZodiac(QueryBuilder $qb, $zodiac) : QueryBuilder
    {
        return $qb->innerJoin('s.zodiacs', 'zodiac', 'WITH', 'zodiac.id = :zodiac')
            ->setParameter('zodiac', $zodiac);
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $letter
     *
     * @return QueryBuilder
     */
    public function filterByLetter(QueryBuilder $qb, string $letter): QueryBuilder
    {
        return $qb->andWhere("s.name LIKE :letter")->setParameter('letter', $letter.'%');
    }

    /**
     * @return mixed
     */
    public function uniqLetterByStone()
    {
        $qb = $this->baseStoneQueryBuilder();

        $qb->select($qb->expr()->substring('s.name', 1, 1))->distinct()->orderBy('s.name');

        return $qb->getQuery()->getResult();
    }
}
