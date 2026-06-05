<?php

namespace PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\PageBundle\Entity\BaseBlock;

/**
 * Class Block
 */
#[ORM\Table(name: 'page_block')]
#[ORM\Entity(repositoryClass: \Doctrine\ORM\EntityRepository::class)]
class Block extends BaseBlock
{
    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
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
