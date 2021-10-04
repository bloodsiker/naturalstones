<?php

namespace GenreBundle\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class Genre
 *
 * @ORM\Entity()
 * @ORM\Table(name="genres")
 * @ORM\HasLifecycleCallbacks
 */
class Genre
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
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    protected $slug;

    /**
     * @var \GenreBundle\Entity\Genre
     *
     * @ORM\ManyToOne(targetEntity="Genre", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $parent;

    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="Genre", mappedBy="parent")
     */
    protected $children;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $countBook;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $isActive;

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
        $this->countBook = 0;

        $this->isActive = true;
        $this->createdAt = new \DateTime('now');
        $this->children = new ArrayCollection();
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
     * @return Genre
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
     * Set slug
     *
     * @param string $slug
     *
     * @return Genre
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
     * Set parent
     *
     * @param \GenreBundle\Entity\Genre $parent
     *
     * @return Genre
     */
    public function setParent(\GenreBundle\Entity\Genre $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \GenreBundle\Entity\Genre
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children.
     *
     * @param \GenreBundle\Entity\Genre $child
     *
     * @return $this
     */
    public function addChild(\GenreBundle\Entity\Genre $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove children.
     *
     * @param \GenreBundle\Entity\Genre $child
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeChild(\GenreBundle\Entity\Genre $child)
    {
        return $this->children->removeElement($child);
    }

    /**
     * Get children.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Genre
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Genre
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
     * Get countBook
     *
     * @return int
     */
    public function getCountBook()
    {
        return $this->countBook;
    }

    /**
     * Set countBook
     *
     * @param int $countBook
     *
     * @return $this
     */
    public function setCountBook(int $countBook)
    {
        $this->countBook = $countBook;

        return $this;
    }
}
