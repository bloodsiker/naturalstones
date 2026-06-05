<?php

namespace ArticleBundle\Entity;

use AppBundle\Traits\TranslatableProxyTrait;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Class Article
 */
#[ORM\Table(name: 'article_article')]
#[ORM\Entity(repositoryClass: \ArticleBundle\Entity\ArticleRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Article implements TranslatableInterface
{
    use ORMBehaviors\Translatable\TranslatableTrait;
    use TranslatableProxyTrait;

    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var \MediaBundle\Entity\MediaImage
     */
    #[ORM\JoinColumn(name: 'image_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: \MediaBundle\Entity\MediaImage::class)]
    protected $image;

    /**
     * @var Category
     */
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: \ArticleBundle\Entity\Category::class)]
    protected $category;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    protected $slug;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer', nullable: false)]
    protected $views;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean', nullable: false)]
    protected $isActive;

    /**
     * @var \DateTime
     */
    #[ORM\Column(type: 'datetime', nullable: false)]
    protected $createdAt;

    /**
     * @var \DateTime
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    protected $updatedAt;

    /**
     * @var ArrayCollection
     */
    #[ORM\JoinTable(name: 'article_article_tags')]
    #[ORM\JoinColumn(name: 'article_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'tag_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: \ShareBundle\Entity\Tag::class, inversedBy: 'articles')]
    protected $tags;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->views = 0;
        $this->isActive = true;
        $this->createdAt = new \DateTime('now');

        $this->tags = new ArrayCollection();
    }

    /**
     * "String" representation of class
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->translate()->getTitle();
    }

    #[ORM\PrePersist]
    public function prePersist()
    {
        if (is_null($this->slug)) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->translate('ru')->getTitle());
        }
    }

    #[ORM\PreUpdate]
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime('now');
        $this->prePersist();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function title()
    {
        return $this->translate()->getTitle();
    }

    public function description()
    {
        return $this->translate()->getDescription();
    }

    public function shortDescription()
    {
        return $this->translate()->getShortDescription();
    }

    /**
     * Set image
     *
     * @return self
     */
    public function setImage(?\MediaBundle\Entity\MediaImage $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \MediaBundle\Entity\MediaImage
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return $this
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return self
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
     * Set views
     *
     * @return $this
     */
    public function setViews(bool $views)
    {
        $this->views = $views;

        return $this;
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
     * Set isActive
     *
     * @param bool $isActive
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
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return $this
     */
    public function setUpdatedAt(?\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;

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
     * Add tags
     *
     * @return self
     */
    public function addTag(\ShareBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;

        return $this;
    }

    /**
     * Remove tags
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
}
