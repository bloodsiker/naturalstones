<?php

namespace OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class OrderBoard
 *
 * @ORM\Entity(repositoryClass="OrderBundle\Entity\OrderBoardRepository")
 * @ORM\Table(name="order_board")
 * @ORM\HasLifecycleCallbacks
 */
class OrderBoard
{
    const STATUS_NEW       = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_CANCEL    = 3;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $bookTitle;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $userName;

    /**
     * @var \UserBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $user;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    protected $status;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $vote;

    /**
     * @var \BookBundle\Entity\Book
     *
     * @ORM\ManyToOne(targetEntity="BookBundle\Entity\Book")
     * @ORM\JoinColumn(name="book_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $book;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->vote = 0;
        $this->status = self::STATUS_NEW;
        $this->createdAt = new \DateTime('now');
    }

    /**
     * "String" representation of class
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->bookTitle;
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
     * Set bookTitle
     *
     * @param string $bookTitle
     *
     * @return $this
     */
    public function setBookTitle($bookTitle)
    {
        $this->bookTitle = $bookTitle;

        return $this;
    }

    /**
     * Get bookTitle
     *
     * @return string
     */
    public function getBookTitle()
    {
        return $this->bookTitle;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     *
     * @return $this
     */
    public function setUser(\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get userName
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Set userName
     *
     * @param string $userName
     *
     * @return $this
     */
    public function setUserName(string $userName)
    {
        $this->userName = $userName;

        return $this;
    }


    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param int $status
     *
     * @return $this
     */
    public function setStatus(int $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get vote
     *
     * @return int
     */
    public function getVote()
    {
        return $this->vote;
    }

    /**
     * Set vote
     *
     * @param int $vote
     *
     * @return $this
     */
    public function setVote(int $vote)
    {
        $this->vote = $vote;

        return $this;
    }

    /**
     * @return int
     */
    public function increaseVote()
    {
        return $this->vote++;
    }

    /**
     * Get book
     *
     * @return \BookBundle\Entity\Book
     */
    public function getBook()
    {
        return $this->book;
    }

    /**
     * Set book
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
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_NEW       => 'new',
            self::STATUS_COMPLETED => 'completed',
            self::STATUS_CANCEL    => 'cancel',
        ];
    }

    /**
     * @param string $status
     *
     * @return mixed
     */
    public static function getNameStatus($status)
    {
        $statuses = [
            'new'       => 'order_board.fields.statuses.new',
            'completed' => 'order_board.fields.statuses.completed',
            'cancel'    => 'order_board.fields.statuses.cancel',
            'top'       => 'order_board.fields.statuses.top',
        ];

        return $statuses[$status];
    }
}
