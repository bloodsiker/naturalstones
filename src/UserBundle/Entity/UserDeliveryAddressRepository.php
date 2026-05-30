<?php

namespace UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use UserBundle\Entity\User;

class UserDeliveryAddressRepository extends EntityRepository
{
    public function clearDefaultForUser(User $user): int
    {
        return $this->createQueryBuilder('a')
            ->update()
            ->set('a.isDefault', ':default')
            ->where('a.user = :user')
            ->setParameter('default', false)
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}
