<?php

namespace BookBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class BookInfoView
 *
 * @ORM\Entity(repositoryClass="BookBundle\Entity\BookInfoViewRepository")
 * @ORM\Table(name="books_info_view")
 * @ORM\HasLifecycleCallbacks
 */
class BookInfoView
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
     * @ORM\Column(type="smallint", length=6, nullable=false)
     */
    protected $views;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", length=6, nullable=false)
     */
    protected $downloads;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", nullable=false)
     */
    protected $viewAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->views     = 0;
        $this->downloads = 0;
        $this->viewAt    = new \DateTime('now');
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
     * Set viewAt.
     *
     * @param \DateTime $viewAt
     *
     * @return $this
     */
    public function setViewAt($viewAt)
    {
        $this->viewAt = $viewAt;

        return $this;
    }

    /**
     * Get downloadAt.
     *
     * @return \DateTime
     */
    public function getViewAt()
    {
        return $this->viewAt;
    }

    /**
     * Get views
     *
     * @return int
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Set views
     *
     * @param int $views
     *
     * @return $this
     */
    public function setViews(int $views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * @return int
     */
    public function doView()
    {
        return $this->views++;
    }

    /**
     * @return int
     */
    public function doDownload()
    {
        return $this->downloads++;
    }

    /**
     * Get downloads
     *
     * @return int
     */
    public function getDownloads()
    {
        return $this->downloads;
    }

    /**
     * Set downloads
     *
     * @param int $downloads
     *
     * @return $this
     */
    public function setDownloads($downloads)
    {
        $this->downloads = $downloads;

        return $this;
    }
}
