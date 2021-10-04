<?php

namespace BookBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class BookInfoDownload
 *
 * @ORM\Entity()
 * @ORM\Table(name="books_info_download")
 * @ORM\HasLifecycleCallbacks
 */
class BookInfoDownload
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
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $downloadAt;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    protected $ip;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->downloadAt = new \DateTime('now');
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
     * Get book
     *
     * @return \BookBundle\Entity\Book
     */
    public function getBook()
    {
        return $this->book;
    }

    /**
     * Set downloadAt.
     *
     * @param \DateTime $downloadAt
     *
     * @return $this
     */
    public function setDownloadAt($downloadAt)
    {
        $this->downloadAt = $downloadAt;

        return $this;
    }

    /**
     * Get downloadAt.
     *
     * @return \DateTime
     */
    public function getDownloadAt()
    {
        return $this->downloadAt;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set ip
     *
     * @param string|null $ip
     *
     * @return $this
     */
    public function setIp(string $ip = null)
    {
        $this->ip = $ip;

        return $this;
    }
}
