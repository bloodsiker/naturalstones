<?php

namespace BookBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class BookVotesResult
 *
 * @ORM\Entity()
 * @ORM\Table(name="books_votes_result")
 * @ORM\HasLifecycleCallbacks
 */
class BookVotesResult
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
     * @var \BookBundle\Entity\Book
     *
     * @ORM\ManyToOne(targetEntity="BookBundle\Entity\Book")
     * @ORM\JoinColumn(name="book_id", referencedColumnName="id", nullable=true, onDelete="cascade")
     */
    protected $book;

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
     * Set poster
     *
     * @param \BookBundle\Entity\Book $book
     *
     * @return $this
     */
    public function setBook(\BookBundle\Entity\Book $book = null)
    {
        $this->book = $book;

        return $this;
    }

    /**
     * Get image
     *
     * @return \BookBundle\Entity\Book
     */
    public function getBook()
    {
        return $this->book;
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
