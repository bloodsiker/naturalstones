<?php

namespace CommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class CommentVotesResult
 *
 * @ORM\Entity()
 * @ORM\Table(name="product_comment_vote_results")
 * @ORM\HasLifecycleCallbacks
 */
class CommentVotesResult
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
     * @var \CommentBundle\Entity\Comment
     *
     * @ORM\ManyToOne(targetEntity="CommentBundle\Entity\Comment")
     * @ORM\JoinColumn(name="comment_id", referencedColumnName="id", nullable=true, onDelete="cascade")
     */
    protected $comment;

    /**
     * @var int
     *
     * @ORM\Column(type="bigint", nullable=true, options={"unsigned"=true})
     */
    protected $ip;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $resultVote;

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
     * Set comment
     *
     * @param \CommentBundle\Entity\Comment $comment
     *
     * @return $this
     */
    public function setComment(\CommentBundle\Entity\Comment $comment = null)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return \CommentBundle\Entity\Comment
     */
    public function getComment()
    {
        return $this->comment;
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
     * Get resultVote
     *
     * @return bool
     */
    public function getResultVote()
    {
        return $this->resultVote;
    }

    /**
     * Set resultVote
     *
     * @param bool $resultVote
     *
     * @return $this
     */
    public function setResultVote(bool $resultVote)
    {
        $this->resultVote = $resultVote;

        return $this;
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
