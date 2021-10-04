<?php

namespace PageBundle\Entity;

use Sonata\PageBundle\Entity\BaseSnapshot as BaseSnapshot;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Snapshot
 *
 * @ORM\Entity
 * @ORM\Table(name="page_snapshot")
 */
class Snapshot extends BaseSnapshot
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Get id.
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }
}
