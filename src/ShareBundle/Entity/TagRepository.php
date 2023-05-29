<?php

namespace ShareBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

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

    public function getTagsByArticles()
    {
        $sql = 'SELECT 
                    t.*
                FROM article_article_tags aat
                INNER JOIN share_tags t
                    ON aat.tag_id = t.id
                WHERE t.is_active = 1
                GROUP BY t.id';

        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(Tag::class, 't');
        $rsm->addFieldResult('t', 'id', 'id');
        $rsm->addFieldResult('t', 'isActive', 'is_active');
        $rsm->addFieldResult('t', 'slug', 'slug');
        return $this->getEntityManager()->createNativeQuery($sql, $rsm)->getResult();
    }
}
