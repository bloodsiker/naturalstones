<?php

namespace ShareBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class StoneRepository extends EntityRepository
{
    public function baseStoneQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.translations', 'st')
            ->addSelect('st')
            ->where('s.isActive = 1')
            ->orderBy('s.id', 'DESC');
    }

    public function filterByShowMain(QueryBuilder $qb): QueryBuilder
    {
        return $qb->andWhere('s.isShowMain = :isShowMain')->setParameter('isShowMain', true);
    }

    public function filterByShowConstructor(QueryBuilder $qb): QueryBuilder
    {
        return $qb->andWhere('s.isShowConstructor = :isShowConstructor')->setParameter('isShowConstructor', true);
    }

    public function filterByZodiac(QueryBuilder $qb, int|string $zodiac): QueryBuilder
    {
        return $qb->innerJoin('s.zodiacs', 'zodiac', 'WITH', 'zodiac.id = :zodiac')
            ->setParameter('zodiac', $zodiac);
    }

    public function filterByLetter(QueryBuilder $qb, string $letter): QueryBuilder
    {
        return $qb
            ->andWhere('st.name LIKE :letter')
            ->setParameter('letter', $letter . '%');
    }

    /**
     * @return list<array<int, string>>
     */
    public function uniqLetterByStone(string $locale): array
    {
        $qb = $this->baseStoneQueryBuilder();
        $qb->select($qb->expr()->substring('st.name', 1, 1))
            ->andWhere('st.locale = :locale')
            ->setParameter('locale', $locale)
            ->distinct()
            ->orderBy('st.name');

        return $qb->getQuery()->getResult();
    }
}