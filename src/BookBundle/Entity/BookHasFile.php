<?php

namespace BookBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class BookHasFile
 *
 * @ORM\Entity()
 * @ORM\Table(name="books_has_file")
 * @ORM\HasLifecycleCallbacks
 */
class BookHasFile
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
     * @ORM\ManyToOne(targetEntity="BookBundle\Entity\Book", inversedBy="bookHasFiles")
     * @ORM\JoinColumn(name="book_id", referencedColumnName="id", nullable=false)
     */
    protected $book;

    /**
     * @var \MediaBundle\Entity\MediaFile
     *
     * @ORM\ManyToOne(targetEntity="MediaBundle\Entity\MediaFile", fetch="EAGER")
     * @ORM\JoinColumn(name="book_file_id", referencedColumnName="id", nullable=false)
     */
    protected $bookFile;

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
     * @return BookHasFile
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
     * @return BookHasFile
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
     * Set bookFile.
     *
     * @param \MediaBundle\Entity\MediaFile $bookFile
     *
     * @return BookHasFile
     */
    public function setBookFile(\MediaBundle\Entity\MediaFile $bookFile = null)
    {
        $this->bookFile = $bookFile;

        return $this;
    }

    /**
     * Get bookFile.
     *
     * @return \MediaBundle\Entity\MediaFile
     */
    public function getBookFile()
    {
        return $this->bookFile;
    }
}
