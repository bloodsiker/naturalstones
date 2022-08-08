<?php

namespace ShareBundle\Entity;

use AppBundle\Traits\TranslatableProxyTrait;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Class Stone
 *
 * @ORM\Entity(repositoryClass="ShareBundle\Entity\StoneRepository")
 * @ORM\Table(name="share_stones")
 * @ORM\HasLifecycleCallbacks
 */
class Stone
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
     * @ORM\ManyToMany(targetEntity="ShareBundle\Entity\Zodiac", inversedBy="stones")
     * @ORM\JoinTable(name="share_stone_zodiacs",
     *     joinColumns={@ORM\JoinColumn(name="stone_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="zodiac_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $zodiacs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->isActive = true;
        $this->isShowMain = true;
        $this->createdAt = new \DateTime('now');

        $this->zodiacs = new ArrayCollection();
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
     * @return Stone
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
     * @return Stone
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
     * @return Stone
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
     * @return Stone
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
     * @return Stone
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
     * Get description
     *
     * @return string
     */
    public function description()
    {
        return (string) $this->translate()->getDescription();
    }

    /**
     * Add zodiac
     *
     * @param \ShareBundle\Entity\Zodiac $zodiac
     *
     * @return Stone
     */
    public function addZodiac(\ShareBundle\Entity\Zodiac $zodiac)
    {
        $this->zodiacs[] = $zodiac;

        return $this;
    }

    /**
     * Remove zodiac
     *
     * @param \ShareBundle\Entity\Zodiac $zodiac
     */
    public function removeZodiac(\ShareBundle\Entity\Zodiac $zodiac)
    {
        $this->zodiacs->removeElement($zodiac);
    }

    /**
     * Get zodiacs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getZodiacs()
    {
        return $this->zodiacs;
    }
}
