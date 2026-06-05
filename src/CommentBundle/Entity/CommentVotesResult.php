<?php

namespace CommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class CommentVotesResult
 */
#[ORM\Table(name: 'product_comment_vote_results')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class CommentVotesResult
{
    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var Comment
     */
    #[ORM\JoinColumn(name: 'comment_id', referencedColumnName: 'id', nullable: true, onDelete: 'cascade')]
    #[ORM\ManyToOne(targetEntity: \CommentBundle\Entity\Comment::class)]
    protected $comment;

    /**
     * @var int
     */
    #[ORM\Column(type: 'bigint', nullable: true, options: ['unsigned' => true])]
    protected $ip;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean', nullable: false)]
    protected $resultVote;

    /**
     * @var \DateTime
     */
    #[ORM\Column(type: 'datetime', nullable: false)]
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set comment
     *
     * @return $this
     */
    public function setComment(?Comment $comment = null)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return Comment
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
