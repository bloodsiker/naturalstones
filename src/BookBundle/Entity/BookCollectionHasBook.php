<?php

namespace BookBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class BookCollectionHasBook
 *
 * @ORM\Entity()
 * @ORM\Table(name="books_collection_has_book")
 * @ORM\HasLifecycleCallbacks
 */
class BookCollectionHasBook
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
     * @var \BookBundle\Entity\BookCollection
     *
     * @ORM\ManyToOne(targetEntity="BookBundle\Entity\BookCollection", inversedBy="collectionHasBook", fetch="EAGER")
     * @ORM\JoinColumn(name="collection_id", referencedColumnName="id", nullable=false)
     */
    protected $collection;

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
     * @return BookCollectionHasBook
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
     * @return BookCollectionHasBook
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
     * Set collection.
     *
     * @param \BookBundle\Entity\BookCollection $collection
     *
     * @return BookCollectionHasBook
     */
    public function setCollection(\BookBundle\Entity\BookCollection $collection = null)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Get collection.
     *
     * @return \BookBundle\Entity\BookCollection
     */
    public function getCollection()
    {
        return $this->collection;
    }
}
