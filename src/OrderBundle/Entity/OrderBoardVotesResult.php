<?php

namespace OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class OrderBoardVotesResult
 *
 * @ORM\Entity()
 * @ORM\Table(name="order_board_votes_result")
 * @ORM\HasLifecycleCallbacks
 */
class OrderBoardVotesResult
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
     * @var \OrderBundle\Entity\OrderBoard
     *
     * @ORM\ManyToOne(targetEntity="OrderBundle\Entity\OrderBoard")
     * @ORM\JoinColumn(name="order_board_id", referencedColumnName="id", nullable=true, onDelete="cascade")
     */
    protected $orderBoard;

    /**
     * @var int
     *
     * @ORM\Column(type="bigint", nullable=true, options={"unsigned"=true})
     */
    protected $ip;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $votedAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->votedAt = new \DateTime('now');
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set orderBoard
     *
     * @param \OrderBundle\Entity\OrderBoard $orderBoard
     *
     * @return $this
     */
    public function setOrderBoard(\OrderBundle\Entity\OrderBoard $orderBoard = null)
    {
        $this->orderBoard = $orderBoard;

        return $this;
    }

    /**
     * Get orderBoard
     *
     * @return \OrderBundle\Entity\OrderBoard
     */
    public function getOrderBoard()
    {
        return $this->orderBoard;
    }

    /**
     * Set ip.
     *
     * @param int|null $ip
     *
     * @return $this
     */
    public function setIp($ip = null)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip.
     *
     * @return int|null
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set votedAt.
     *
     * @param \DateTime $votedAt
     *
     * @return $this
     */
    public function setVotedAt($votedAt)
    {
        $this->votedAt = $votedAt;

        return $this;
    }

    /**
     * Get votedAt.
     *
     * @return \DateTime
     */
    public function getVotedAt()
    {
        return $this->votedAt;
    }
}
