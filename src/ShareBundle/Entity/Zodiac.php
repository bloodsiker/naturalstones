<?php

namespace ShareBundle\Entity;

use AppBundle\Traits\TranslatableProxyTrait;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Class Zodiac
 *
 * @ORM\Entity(repositoryClass="ShareBundle\Entity\ZodiacRepository")
 * @ORM\Table(name="share_zodiacs")
 * @ORM\HasLifecycleCallbacks
 */
class Zodiac
{
    use ORMBehaviors\Translatable\Translatable;
    use TranslatableProxyTrait;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \MediaBundle\Entity\MediaImage
     *
     * @ORM\ManyToOne(targetEntity="MediaBundle\Entity\MediaImage")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $image;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    protected $slug;

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
    protected $isShowMain;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="ShareBundle\Entity\Stone", mappedBy="tags")
     */
    protected $stones;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->isActive = true;
        $this->isShowMain = true;
        $this->createdAt = new \DateTime('now');

        $this->stones = new ArrayCollection();
    }

    /**
     * "String" representation of class
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->translate()->getName();
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        if (is_null($this->slug)) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->translate()->getName());
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
     * Set image
     *
     * @param \MediaBundle\Entity\MediaImage $image
     *
     * @return Zodiac
     */
    public function setImage(\MediaBundle\Entity\MediaImage $image = null)
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
     * Get name
     *
     * @return string
     */
    public function name()
    {
        return (string) $this->translate()->getName();
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Zodiac
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Zodiac
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
     * Set isShowMain
     *
     * @param boolean $isShowMain
     *
     * @return Zodiac
     */
    public function setIsShowMain($isShowMain)
    {
        $this->isShowMain = $isShowMain;

        return $this;
    }

    /**
     * Get isShowMain
     *
     * @return boolean
     */
    public function getIsShowMain()
    {
        return $this->isShowMain;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Zodiac
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
     * Add product
     *
     * @param \ShareBundle\Entity\Stone $product
     *
     * @return $this
     */
    public function addStone(\ShareBundle\Entity\Stone $stone)
    {
        $this->stones[] = $stone;

        return $this;
    }

    /**
     * Remove stone
     *
     * @param \ShareBundle\Entity\Stone $stone
     */
    public function removeStone(\ShareBundle\Entity\Stone $stone)
    {
        $this->stones->removeElement($stone);
    }

    /**
     * Get stones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStones()
    {
        return $this->stones;
    }
}
