<?php

namespace BookBundle\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class Book
 *
 * @ORM\Entity(repositoryClass="BookBundle\Entity\BookRepository")
 * @ORM\Table(name="books")
 * @ORM\HasLifecycleCallbacks
 *
 * @Vich\Uploadable
 */
class Book
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
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @var \MediaBundle\Entity\MediaImage
     *
     * @ORM\ManyToOne(targetEntity="MediaBundle\Entity\MediaImage")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $poster;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    protected $slug;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     */
    protected $description;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", length=4, nullable=true)
     */
    protected $year;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", length=3, nullable=true)
     */
    protected $restrictAge;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", length=4, nullable=true)
     */
    protected $seriesNumber;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", length=4, nullable=true)
     */
    protected $pages;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $isActive;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $isAllowDownload;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $download;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $views;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", length=7, nullable=true)
     */
    protected $ratePlus;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", length=7, nullable=true)
     */
    protected $rateMinus;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="GenreBundle\Entity\Genre")
     * @ORM\JoinTable(name="books_genres",
     *     joinColumns={@ORM\JoinColumn(name="book_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="genre_id", referencedColumnName="id", onDelete="CASCADE")}
     *     )
     */
    protected $genres;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="BookBundle\Entity\BookHasFile",
     *     mappedBy="book", cascade={"all"}, orphanRemoval=true
     * )
     * @ORM\OrderBy({"orderNum" = "ASC"})
     */
    protected $bookHasFiles;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="BookBundle\Entity\BookHasRelated",
     *     mappedBy="book", cascade={"all"}, orphanRemoval=true
     * )
     * @ORM\OrderBy({"orderNum" = "ASC"})
     */
    protected $bookHasRelated;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="ShareBundle\Entity\Tag", inversedBy="books")
     * @ORM\JoinTable(name="book_tags",
     *     joinColumns={@ORM\JoinColumn(name="book_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $tags;

    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="CommentBundle\Entity\Comment", mappedBy="book")
     */
    protected $comments;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    protected $isbn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updatedAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->isActive = true;
        $this->isAllowDownload = true;
        $this->download = 0;
        $this->views = 0;
        $this->ratePlus = 0;
        $this->rateMinus = 0;
        $this->createdAt = new \DateTime('now');

        $this->genres         = new ArrayCollection();
        $this->tags           = new ArrayCollection();
        $this->bookHasFiles   = new ArrayCollection();
        $this->bookHasRelated = new ArrayCollection();
        $this->comments       = new ArrayCollection();
    }

    /**
     * "String" representation of class
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->name;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        if (is_null($this->slug)) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->getName());
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime('now');

        $this->prePersist();
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
     * Set name
     *
     * @param string $name
     *
     * @return Book
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set poster
     *
     * @param \MediaBundle\Entity\MediaImage $poster
     *
     * @return Book
     */
    public function setPoster(\MediaBundle\Entity\MediaImage $poster = null)
    {
        $this->poster = $poster;

        return $this;
    }

    /**
     * Get image
     *
     * @return \MediaBundle\Entity\MediaImage
     */
    public function getPoster()
    {
        return $this->poster;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Book
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Book
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set year
     *
     * @param string $year
     *
     * @return Book
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Get restrictAge
     *
     * @return int
     */
    public function getRestrictAge()
    {
        return $this->restrictAge;
    }

    /**
     * Set restrictAge
     *
     * @param int $restrictAge
     *
     * @return $this
     */
    public function setRestrictAge($restrictAge)
    {
        $this->restrictAge = $restrictAge;

        return $this;
    }

    /**
     * Set pages
     *
     * @param string $pages
     *
     * @return Book
     */
    public function setPages($pages)
    {
        $this->pages = $pages;

        return $this;
    }

    /**
     * Get pages
     *
     * @return integer
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Book
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set download
     *
     * @param boolean $download
     *
     * @return Book
     */
    public function setDownload($download)
    {
        $this->download = $download;

        return $this;
    }

    /**
     * Get download
     *
     * @return integer
     */
    public function getDownload()
    {
        return $this->download;
    }

    /**
     * Set views
     *
     * @param boolean $views
     *
     * @return Book
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return integer
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Add genres
     *
     * @param \GenreBundle\Entity\Genre $genres
     *
     * @return Book
     */
    public function addGenre(\GenreBundle\Entity\Genre $genres)
    {
        $this->genres[] = $genres;

        return $this;
    }

    /**
     * Remove genres
     *
     * @param \GenreBundle\Entity\Genre $genres
     */
    public function removeGenre(\GenreBundle\Entity\Genre $genres)
    {
        $this->genres->removeElement($genres);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * Add bookHasFiles.
     *
     * @param \BookBundle\Entity\BookHasFile $bookHasFiles
     *
     * @return Book
     */
    public function addBookHasFile(\BookBundle\Entity\BookHasFile $bookHasFiles)
    {
        $bookHasFiles->setBook($this);
        $this->bookHasFiles[] = $bookHasFiles;

        return $this;
    }

    /**
     * Remove bookHasFiles.
     *
     * @param \BookBundle\Entity\BookHasFile $bookHasFiles
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeBookHasFile(\BookBundle\Entity\BookHasFile $bookHasFiles)
    {
        return $this->bookHasFiles->removeElement($bookHasFiles);
    }

    /**
     * Get BookHasAuthors.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBookHasFiles()
    {
        return $this->bookHasFiles;
    }

    /**
     * Add BookHasRelated.
     *
     * @param \BookBundle\Entity\BookHasRelated $bookHasRelated
     *
     * @return Book
     */
    public function addBookHasRelated(\BookBundle\Entity\BookHasRelated $bookHasRelated)
    {
        $bookHasRelated->setBook($this);
        $this->bookHasRelated[] = $bookHasRelated;

        return $this;
    }

    /**
     * Remove bookHasRelated.
     *
     * @param \BookBundle\Entity\BookHasRelated $bookHasRelated
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeBookHasRelated(\BookBundle\Entity\BookHasRelated $bookHasRelated)
    {
        return $this->bookHasRelated->removeElement($bookHasRelated);
    }

    /**
     * Get BookHasRelated.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBookHasRelated()
    {
        return $this->bookHasRelated;
    }

    /**
     * Add tags
     *
     * @param \ShareBundle\Entity\Tag $tags
     *
     * @return Book
     */
    public function addTag(\ShareBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;

        return $this;
    }

    /**
     * Remove tags
     *
     * @param \ShareBundle\Entity\Tag $tags
     */
    public function removeTag(\ShareBundle\Entity\Tag $tags)
    {
        $this->tags->removeElement($tags);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set isbn
     *
     * @param \DateTime $isbn
     *
     * @return Book
     */
    public function setIsbn($isbn)
    {
        $this->isbn = $isbn;

        return $this;
    }

    /**
     * Get isbn
     *
     * @return string
     */
    public function getIsbn()
    {
        return $this->isbn;
    }

    /**
     * Add comment.
     *
     * @param \CommentBundle\Entity\Comment $comment
     *
     * @return $this
     */
    public function addComment(\CommentBundle\Entity\Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment.
     *
     * @param \CommentBundle\Entity\Comment $comment
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeComment(\CommentBundle\Entity\Comment $comment)
    {
        return $this->comments->removeElement($comment);
    }

    /**
     * Get children.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Book
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Book
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Get isAllowDownload
     *
     * @return bool
     */
    public function isAllowDownload()
    {
        return $this->isAllowDownload;
    }

    /**
     * Set isAllowDownload
     *
     * @param bool $isAllowDownload
     *
     * @return Book
     */
    public function setIsAllowDownload(bool $isAllowDownload)
    {
        $this->isAllowDownload = $isAllowDownload;

        return $this;
    }

    /**
     * Get rating book
     * @return string
     */
    public function rating()
    {
        $rate = $this->ratePlus - $this->rateMinus;

        return '[+'.$this->ratePlus.'] - [-'.$this->rateMinus.'] => ['.$rate.']';
    }

    /**
     * Get ratePlus
     *
     * @return int
     */
    public function getRatePlus()
    {
        return $this->ratePlus;
    }

    /**
     * Set ratePlus
     *
     * @param int $ratePlus
     *
     * @return $this
     */
    public function setRatePlus(int $ratePlus)
    {
        $this->ratePlus = $ratePlus;

        return $this;
    }

    /**
     * Get rateMinus
     *
     * @return int
     */
    public function getRateMinus()
    {
        return $this->rateMinus;
    }

    /**
     * Set rateMinus
     *
     * @param int $rateMinus
     *
     * @return $this
     */
    public function setRateMinus(int $rateMinus)
    {
        $this->rateMinus = $rateMinus;

        return $this;
    }
}
