<?php

namespace AppBundle\Block;

use Doctrine\ORM\EntityManagerInterface;
use ProductBundle\Entity\ProductSearchHistory;

/**
 * Shared search-history logic for the {@see SearchBlockService} and {@see SearchCategoryBlockService}.
 */
trait SearchHistoryTrait
{
    abstract protected function getEntityManager(): EntityManagerInterface;

    protected function saveSearchHistory(string $search, ?string $ip): void
    {
        if (strlen($search) < 2) {
            return;
        }

        $ip = $ip ? substr($ip, 0, 20) : null;
        $em = $this->getEntityManager();

        $qb = $em->getRepository(ProductSearchHistory::class)
            ->createQueryBuilder('history')
            ->where('history.search = :search')
            ->andWhere('history.createdAt >= :createdAt')
            ->setParameter('search', $search)
            ->setParameter('createdAt', new \DateTime('-15 minutes'))
            ->setMaxResults(1);

        if ($ip) {
            $qb->andWhere('history.ip = :ip')->setParameter('ip', $ip);
        } else {
            $qb->andWhere('history.ip IS NULL');
        }

        if ($qb->getQuery()->getOneOrNullResult()) {
            return;
        }

        $history = new ProductSearchHistory();
        $history->setSearch($search);
        $history->setIp($ip);

        $em->persist($history);
        $em->flush();
    }
}