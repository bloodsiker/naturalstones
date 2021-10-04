<?php


namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\UserBundle\Entity\BaseGroup as BaseGroup;

/**
 * Class Group
 * @package UserBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="user_groups")
 */
class Group extends BaseGroup
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }
}
