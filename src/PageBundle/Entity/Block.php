<?php

namespace PageBundle\Entity;

use Sonata\PageBundle\Entity\BaseBlock as BaseBlock;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Block
 *
 * @ORM\Entity(repositoryClass="Doctrine\ORM\EntityRepository")
 * @ORM\Table(name="page_block")
 */
class Block extends BaseBlock
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
