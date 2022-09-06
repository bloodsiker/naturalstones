<?php

namespace ProductBundle\Entity;

use AppBundle\Traits\TranslatableProxyTrait;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Class Category
 *
 * @ORM\Entity(repositoryClass="ProductBundle\Entity\CategoryRepository")
 * @ORM\Table(name="product_category")
 * @ORM\HasLifecycleCallbacks
 */
class Category
{
    use ORMBehaviors\Translatable\Translatable;
    use TranslatableProxyTrait;

    const TYPE_MAIN      = 1;
    const TYPE_SECONDARY = 2;
    const TYPE_INDIVIDUAL = 3;
    const TYPE_GIFT_BOX = 4;
    const TYPE_SCRAPERS = 5;
    const TYPE_GEMATIT = 6;

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
     * @ORM\Column(type="smallint", nullable=false)
     */
    protected $type;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $isActive;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ProductBundle\Entity\Product", mappedBy="category")
     */
    protected $products;

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
        $this->isActive = true;
        $this->type = 1;
        $this->orderNum = 0;

        $this->products = new ArrayCollection();
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
            $this->slug = $slugify->slugify($this->translate('ru')->getName());
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
     * Get name
     *
     * @return string
     */
    public function name()
    {
        return $this->translate()->getName();
    }

    /**
     * Set image
     *
     * @param \MediaBundle\Entity\MediaImage $image
     *
     * @return $this
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
     * Get description
     *
     * @return string
     */
    public function description()
    {
        return $this->translate()->getDescription();
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Category
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
     * Set type
     *
     * @param int $type
     *
     * @return Category
     */
    public function setType(int $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Category
     */
    public function setIsActive(bool $isActive)
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
     * Add products
     *
     * @param \ProductBundle\Entity\Product $product
     *
     * @return $this
     */
    public function addProduct(\ProductBundle\Entity\Product $product)
    {
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove products
     *
     * @param \ProductBundle\Entity\Product $product
     */
    public function removeProduct(\ProductBundle\Entity\Product $product)
    {
        $this->products->removeElement($product);
    }

    /**
     * Get booksPublishing
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Set orderNum.
     *
     * @param int $orderNum
     *
     * @return $this
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

    public static function getTypes()
    {
        return [
            self::TYPE_MAIN => 'main',
            self::TYPE_SECONDARY => 'secondary',
            self::TYPE_INDIVIDUAL => 'individual',
            self::TYPE_GIFT_BOX => 'gift_box',
            self::TYPE_SCRAPERS => 'scrapers',
            self::TYPE_GEMATIT => 'gematit',
        ];
    }
}
