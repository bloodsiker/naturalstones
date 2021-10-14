<?php

namespace MainImageBundle\Entity;

use AppBundle\Traits\OrderNumLogicTrait;
use Doctrine\ORM\EntityRepository;

/**
 * Class MainImageRepository
 */
class MainImageRepository extends EntityRepository
{
    use OrderNumLogicTrait;
}
