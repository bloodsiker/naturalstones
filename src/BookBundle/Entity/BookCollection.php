<?php

namespace BookBundle\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class BookCollection
 *
 * @ORM\Entity(repositoryClass="BookBundle\Entity\BookCollectionRepository")
 * @ORM\Table(name="books_collection")
 * @ORM\HasLifecycleCallbacks
 *
 * @Vich\Uploadable
 */
class BookCollection
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
    protected $title;

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
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $isActive;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $views;

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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="BookBundle\Entity\BookCollectionHasBook",
     *     mappedBy="collection", cascade={"all"}, orphanRemoval=true
     * )
     * @ORM\OrderBy({"orderNum" = "ASC"})
     */
    protected $collectionHasBook;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="GenreBundle\Entity\Genre")
     * @ORM\JoinTable(name="books_collection_genres",
     *     joinColumns={@ORM\JoinColumn(name="collection_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="genre_id", referencedColumnName="id", onDelete="CASCADE")}
     *     )
     */
    protected $genres;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->isActive = true;
        $this->views = 0;
        $this->createdAt = new \DateTime('now');

        $this->collectionHasBook = new ArrayCollection();
        $this->genres            = new ArrayCollection();
    }

    /**
     * "String" representation of class
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->title;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        if (is_null($this->slug)) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->getTitle());
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
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set poster
     *
     * @param \MediaBundle\Entity\MediaImage $poster
     *
     * @return $this
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
     * @return $this
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
     * @return $this
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return $this
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
     * Set views
     *
     * @param boolean $views
     *
     * @return $this
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return $this
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
     * Add collectionHasBook.
     *
     * @param \BookBundle\Entity\BookCollectionHasBook $bookCollectionHasBook
     *
     * @return BookCollection
     */
    public function addCollectionHasBook(\BookBundle\Entity\BookCollectionHasBook $bookCollectionHasBook)
    {
        $bookCollectionHasBook->setCollection($this);
        $this->collectionHasBook[] = $bookCollectionHasBook;

        return $this;
    }

    /**
     * Remove collectionHasBook.
     *
     * @param \BookBundle\Entity\BookCollectionHasBook $bookCollectionHasBook
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeCollectionHasBook(\BookBundle\Entity\BookCollectionHasBook $bookCollectionHasBook)
    {
        return $this->collectionHasBook->removeElement($bookCollectionHasBook);
    }

    /**
     * Get articleHasBook.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCollectionHasBook()
    {
        return $this->collectionHasBook;
    }

    /**
     * Add genres
     *
     * @param \GenreBundle\Entity\Genre $genres
     *
     * @return BookCollection
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
}
