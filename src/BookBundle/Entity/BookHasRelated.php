<?php

namespace BookBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class BookHasRelated
 *
 * @ORM\Entity()
 * @ORM\Table(name="books_has_related")
 * @ORM\HasLifecycleCallbacks
 */
class BookHasRelated
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
     * @ORM\ManyToOne(targetEntity="BookBundle\Entity\Book", inversedBy="bookHasRelated")
     * @ORM\JoinColumn(name="book_id", referencedColumnName="id", nullable=false)
     */
    protected $book;

    /**
     * @var \BookBundle\Entity\Book
     *
     * @ORM\ManyToOne(targetEntity="BookBundle\Entity\Book", fetch="EAGER")
     * @ORM\JoinColumn(name="related_book_id", referencedColumnName="id", nullable=false)
     */
    protected $relatedBook;

    /**
     * @var int
     *
     * @ORM\Column(name="order_num", type="integer", nullable=false, options={"default": 1})
     */
    protected $orderNum;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->orderNum = 0;
    }

    /**
     * "String" representation of class
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->book;
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
     * Set orderNum.
     *
     * @param int $orderNum
     *
     * @return BookHasRelated
     */
    public function setOrderNum($orderNum)
    {
        $this->orderNum = $orderNum;

        return $this;
    }

    /**
     * Get orderNum.
     *
     * @return int
     */
    public function getOrderNum()
    {
        return $this->orderNum;
    }

    /**
     * Set book.
     *
     * @param \BookBundle\Entity\Book $book
     *
     * @return BookHasRelated
     */
    public function setBook(\BookBundle\Entity\Book $book)
    {
        $this->book = $book;

        return $this;
    }

    /**
     * Get book.
     *
     * @return \BookBundle\Entity\Book
     */
    public function getBook()
    {
        return $this->book;
    }

    /**
     * Set relatedBook.
     *
     * @param \BookBundle\Entity\Book $relatedBook
     *
     * @return BookHasRelated
     */
    public function setRelatedBook(\BookBundle\Entity\Book $relatedBook = null)
    {
        $this->relatedBook = $relatedBook;

        return $this;
    }

    /**
     * Get relatedBook.
     *
     * @return \BookBundle\Entity\Book
     */
    public function getRelatedBook()
    {
        return $this->relatedBook;
    }
}
