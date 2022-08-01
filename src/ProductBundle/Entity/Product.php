<?php

namespace ProductBundle\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class Product
 *
 * @ORM\Entity(repositoryClass="ProductBundle\Entity\ProductRepository")
 * @ORM\Table(name="product_product")
 * @ORM\HasLifecycleCallbacks()
 *
 * @Vich\Uploadable
 */
class Product
{
    const WHO_MAN = 'man';
    const WHO_WOMAN = 'woman';

    const TYPE_LETTERS = 1;
    const TYPE_INSERTS = 2;
    const TYPE_PENDANTS = 3;

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
    protected $image;

    /**
     * @var \ProductBundle\Entity\Category
     *
     * @ORM\ManyToOne(targetEntity="ProductBundle\Entity\Category", inversedBy="products")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $category;

    /**
     * @var \ProductBundle\Entity\ProductOptionLabel
     *
     * @ORM\ManyToOne(targetEntity="ProductBundle\Entity\ProductOptionLabel")
     * @ORM\JoinColumn(name="option_label_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $optionLabel;

    /**
     * @var \ShareBundle\Entity\Size
     *
     * @ORM\ManyToOne(targetEntity="ShareBundle\Entity\Size")
     * @ORM\JoinColumn(name="size_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $size;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    protected $slug;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    protected $instagram_link;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     */
    protected $description;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    protected $price;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    protected $discount;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    protected $percent;

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
    protected $isMan;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $isWoman;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $isAvailable;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $isMainProduct;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $views;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $productGroup;

    /**
     * @var bool
     *
     * @ORM\Column(type="smallint", nullable=false)
     */
    protected $optionType;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="ShareBundle\Entity\Colour", inversedBy="products")
     * @ORM\JoinTable(name="product_product_colours",
     *     joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="colour_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $colours;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="ShareBundle\Entity\Metal")
     * @ORM\JoinTable(name="product_product_metals",
     *     joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="metal_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $metals;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="ShareBundle\Entity\Tag", inversedBy="products")
     * @ORM\JoinTable(name="product_product_tags",
     *     joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $tags;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="ShareBundle\Entity\Stone", inversedBy="products")
     * @ORM\JoinTable(name="product_product_stones",
     *     joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="stone_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $stones;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ProductBundle\Entity\ProductHasImage",
     *     mappedBy="product", cascade={"all"}, orphanRemoval=true
     * )
     * @ORM\OrderBy({"orderNum" = "ASC"})
     */
    protected $productHasImage;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ProductBundle\Entity\ProductHasProduct",
     *     mappedBy="product", cascade={"all"}, orphanRemoval=true
     * )
     * @ORM\OrderBy({"orderNum" = "ASC"})
     */
    protected $productHasProduct;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ProductBundle\Entity\ProductHasOption",
     *     mappedBy="product", cascade={"all"}, orphanRemoval=true
     * )
     * @ORM\OrderBy({"orderNum" = "ASC"})
     */
    protected $productHasOption;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ProductBundle\Entity\ProductHasOptionMetal",
     *     mappedBy="product", cascade={"all"}, orphanRemoval=true
     * )
     * @ORM\OrderBy({"orderNum" = "ASC"})
     */
    protected $productHasOptionMetal;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ProductBundle\Entity\ProductHasOptionColour",
     *     mappedBy="product", cascade={"all"}, orphanRemoval=true
     * )
     * @ORM\OrderBy({"orderNum" = "ASC"})
     */
    protected $productHasOptionColour;

    /**
     * @var int
     *
     * @ORM\Column(name="order_num", type="integer", nullable=false, options={"default": 1})
     */
    protected $orderNum;

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
     * @var float
     */
    protected $finalPrice;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->size          = null;
        $this->isActive      = true;
        $this->isAvailable   = true;
        $this->isMan         = false;
        $this->isWoman       = true;
        $this->isMainProduct = true;
        $this->views      = 0;
        $this->price      = 0;
        $this->discount   = 0;
        $this->percent    = 0;
        $this->finalPrice = 0;
        $this->orderNum   = 1;
        $this->createdAt  = new \DateTime('now');

        $this->colours           = new ArrayCollection();
        $this->metals            = new ArrayCollection();
        $this->tags              = new ArrayCollection();
        $this->stones            = new ArrayCollection();
        $this->productHasImage   = new ArrayCollection();
        $this->productHasProduct = new ArrayCollection();
        $this->productHasOption       = new ArrayCollection();
        $this->productHasOptionMetal  = new ArrayCollection();
        $this->productHasOptionColour = new ArrayCollection();
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

    public function __clone()
    {
        if ($this->id) {
            $this->setId(null);
            $this->setSize(null);
            $this->setViews(0);
            $this->setPrice(0);
            $this->setDiscount(0);
            $this->setPercent(0);
            $this->setIsMainProduct(0);
        }
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
        if (empty($this->discount)) {
            $this->discount = 0;
        }

        if (empty($this->price)) {
            $this->price = 0;
        }

        if (empty($this->percent)) {
            $this->percent = 0;
        }

        if ($this->percent && !$this->discount) {
            $this->discount = round($this->price - ($this->price * $this->percent / 100), 0);
        }

        $this->prePersist();
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * @return Product
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
     * Set instagram_link
     *
     * @param string $link
     *
     * @return Product
     */
    public function setInstagramLink($link)
    {
        $this->instagram_link = $link;

        return $this;
    }

    /**
     * Get instagram_link
     *
     * @return string
     */
    public function getInstagramLink()
    {
        return $this->instagram_link;
    }

    /**
     * Set image
     *
     * @param \MediaBundle\Entity\MediaImage $image
     *
     * @return Product
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
     * Set category
     *
     * @param \ProductBundle\Entity\Category $category
     *
     * @return Product
     */
    public function setCategory(\ProductBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \ProductBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set optionLabel
     *
     * @param \ProductBundle\Entity\ProductOptionLabel $optionLabel
     *
     * @return Product
     */
    public function setOptionLabel(\ProductBundle\Entity\ProductOptionLabel $optionLabel = null)
    {
        $this->optionLabel = $optionLabel;

        return $this;
    }

    /**
     * Get optionLabel
     *
     * @return \ProductBundle\Entity\ProductOptionLabel
     */
    public function getOptionLabel()
    {
        return $this->optionLabel;
    }

    /**
     * Set size
     *
     * @param \ShareBundle\Entity\Size $size
     *
     * @return Product
     */
    public function setSize(\ShareBundle\Entity\Size $size = null)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return \ShareBundle\Entity\Size
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Product
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
     * @return Product
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
     * @return Product
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
     * Set isAvailable
     *
     * @param boolean $isAvailable
     *
     * @return Product
     */
    public function setIsAvailable($isAvailable)
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    /**
     * Get isAvailable
     *
     * @return boolean
     */
    public function getIsAvailable()
    {
        return $this->isAvailable;
    }

    /**
     * Set isMan
     *
     * @param boolean $isMan
     *
     * @return Product
     */
    public function setIsMan($isMan)
    {
        $this->isMan = $isMan;

        return $this;
    }

    /**
     * Get isMan
     *
     * @return boolean
     */
    public function getIsMan()
    {
        return $this->isMan;
    }

    /**
     * Set isWoman
     *
     * @param  boolean  $isWoman
     *
     * @return Product
     */
    public function setIsWoman(bool $isWoman)
    {
        $this->isWoman = $isWoman;

        return $this;
    }

    /**
     * Get isWoman
     *
     * @return boolean
     */
    public function getIsWoman()
    {
        return $this->isWoman;
    }

    /**
     * Set isMainProduct
     *
     * @param  boolean  $isMainProduct
     *
     * @return Product
     */
    public function setIsMainProduct(bool $isMainProduct)
    {
        $this->isMainProduct = $isMainProduct;

        return $this;
    }

    /**
     * Get isMainProduct
     *
     * @return boolean
     */
    public function getIsMainProduct()
    {
        return $this->isMainProduct;
    }

    /**
     * Set views
     *
     * @param  boolean  $views
     *
     * @return Product
     */
    public function setViews(bool $views)
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
     * Set productGroup
     *
     * @param  int|null  $productGroup
     *
     * @return Product
     */
    public function setProductGroup(int $productGroup = null)
    {
        $this->productGroup = $productGroup;

        return $this;
    }

    /**
     * Get productGroup
     *
     * @return integer
     */
    public function getProductGroup()
    {
        return $this->productGroup;
    }

    /**
     * Add colours
     *
     * @param \ShareBundle\Entity\Colour $colour
     *
     * @return Product
     */
    public function addColour(\ShareBundle\Entity\Colour $colour)
    {
        $this->colours[] = $colour;

        return $this;
    }

    /**
     * Remove colours
     *
     * @param \ShareBundle\Entity\Colour $colour
     */
    public function removeColour(\ShareBundle\Entity\Colour $colour)
    {
        $this->colours->removeElement($colour);
    }

    /**
     * Get colours
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getColours()
    {
        return $this->colours;
    }

    /**
     * Add metals
     *
     * @param \ShareBundle\Entity\Metal $metal
     *
     * @return Product
     */
    public function addMetal(\ShareBundle\Entity\Metal $metal)
    {
        $this->metals[] = $metal;

        return $this;
    }

    /**
     * Remove colours
     *
     * @param \ShareBundle\Entity\Metal $metal
     */
    public function removeMetal(\ShareBundle\Entity\Metal $metal)
    {
        $this->metals->removeElement($metal);
    }

    /**
     * Get metals
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMetals()
    {
        return $this->metals;
    }

    /**
     * Add tags
     *
     * @param \ShareBundle\Entity\Tag $tags
     *
     * @return Product
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
     * Add stones
     *
     * @param \ShareBundle\Entity\Stone $stone
     *
     * @return Product
     */
    public function addStone(\ShareBundle\Entity\Stone $stone)
    {
        $this->stones[] = $stone;

        return $this;
    }

    /**
     * Remove stones
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

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Product
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
     * @return Product
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
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set price
     *
     * @param  float  $price
     *
     * @return $this
     */
    public function setPrice($price = 0)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get discount
     *
     * @return float
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set discount
     *
     * @param  float  $discount
     *
     * @return $this
     */
    public function setDiscount($discount = 0)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get percent
     *
     * @return float
     */
    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * Set percent
     *
     * @param  float  $percent
     *
     * @return $this
     */
    public function setPercent($percent = 0)
    {
        $this->percent = $percent;

        return $this;
    }

    /**
     * Set optionType
     *
     * @param int $optionType
     *
     * @return $this
     */
    public function setOptionType(int $optionType)
    {
        $this->optionType = $optionType;

        return $this;
    }

    /**
     * Get optionType
     *
     * @return int
     */
    public function getOptionType()
    {
        return $this->optionType;
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

    /**
     * Add ProductHasImage.
     *
     * @param \ProductBundle\Entity\ProductHasImage $productHasImage
     *
     * @return Product
     */
    public function addProductHasImage(\ProductBundle\Entity\ProductHasImage $productHasImage)
    {
        $productHasImage->setProduct($this);
        $this->productHasImage[] = $productHasImage;

        return $this;
    }

    /**
     * Remove ProductHasImage.
     *
     * @param \ProductBundle\Entity\ProductHasImage $productHasImage
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeProductHasImage(\ProductBundle\Entity\ProductHasImage $productHasImage)
    {
        return $this->productHasImage->removeElement($productHasImage);
    }

    /**
     * Get ProductHasImage.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductHasImage()
    {
        return $this->productHasImage;
    }

    /**
     * Add productHasProduct.
     *
     * @param \ProductBundle\Entity\ProductHasProduct $productHasProduct
     *
     * @return Product
     */
    public function addProductHasProduct(\ProductBundle\Entity\ProductHasProduct $productHasProduct)
    {
        $productHasProduct->setProduct($this);
        $this->productHasProduct[] = $productHasProduct;

        return $this;
    }

    /**
     * Remove productHasProduct.
     *
     * @param \ProductBundle\Entity\ProductHasProduct $productHasProduct
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeProductHasProduct(\ProductBundle\Entity\ProductHasProduct $productHasProduct)
    {
        return $this->productHasProduct->removeElement($productHasProduct);
    }

    /**
     * Get productHasProduct.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductHasProduct()
    {
        return $this->productHasProduct;
    }

    /**
     * Add productHasOption.
     *
     * @param \ProductBundle\Entity\ProductHasOption $productHasOption
     *
     * @return Product
     */
    public function addProductHasOption(\ProductBundle\Entity\ProductHasOption $productHasOption)
    {
        $productHasOption->setProduct($this);
        $this->productHasOption[] = $productHasOption;

        return $this;
    }

    /**
     * Remove productHasOption.
     *
     * @param \ProductBundle\Entity\ProductHasOption $productHasOption
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeProductHasOption(\ProductBundle\Entity\ProductHasOption $productHasOption)
    {
        return $this->productHasOption->removeElement($productHasOption);
    }

    /**
     * Get productHasOption.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductHasOption()
    {
        return $this->productHasOption;
    }

    /**
     * Add productHasOptionMetal.
     *
     * @param \ProductBundle\Entity\ProductHasOptionMetal $productHasOptionMetal
     *
     * @return Product
     */
    public function addProductHasOptionMetal(\ProductBundle\Entity\ProductHasOptionMetal $productHasOptionMetal)
    {
        $productHasOptionMetal->setProduct($this);
        $this->productHasOptionMetal[] = $productHasOptionMetal;

        return $this;
    }

    /**
     * Remove productHasOptionMetal.
     *
     * @param \ProductBundle\Entity\ProductHasOptionMetal $productHasOptionMetal
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeProductHasOptionMetal(\ProductBundle\Entity\ProductHasOptionMetal $productHasOptionMetal)
    {
        return $this->productHasOptionMetal->removeElement($productHasOptionMetal);
    }

    /**
     * Get productHasOptionMetal.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductHasOptionMetal()
    {
        return $this->productHasOptionMetal;
    }

    /**
     * Add productHasOptionColour.
     *
     * @param \ProductBundle\Entity\ProductHasOptionColour $productHasOptionColour
     *
     * @return Product
     */
    public function addProductHasOptionColour(\ProductBundle\Entity\ProductHasOptionColour $productHasOptionColour)
    {
        $productHasOptionColour->setProduct($this);
        $this->productHasOptionColour[] = $productHasOptionColour;

        return $this;
    }

    /**
     * Remove productHasOptionColour.
     *
     * @param \ProductBundle\Entity\ProductHasOptionColour $productHasOptionColour
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeProductHasOptionColour(\ProductBundle\Entity\ProductHasOptionColour $productHasOptionColour)
    {
        return $this->productHasOptionColour->removeElement($productHasOptionColour);
    }

    /**
     * Get productHasOptionColour.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductHasOptionColour()
    {
        return $this->productHasOptionColour;
    }

    public static $whois = [
        self::WHO_MAN => 'Для мужчин',
        self::WHO_WOMAN => 'Для женщин',
    ];

    public static function getTypes()
    {
        return [
            self::TYPE_LETTERS => 'letters',
            self::TYPE_INSERTS => 'inserts',
            self::TYPE_PENDANTS => 'pendants',
        ];
    }

    public function setFinalPrice($colour = null)
    {
        $this->finalPrice = $this->discount ?: $this->price;

        if ($colour) {
            foreach ($this->productHasOptionColour as $col) {
                if ($colour->getId() === $col->getColour()->getId()) {
                    if ($col->getPrice() > 0) {
                        $this->finalPrice = $col->getPrice();
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @return float|int
     */
    public function getFinalPrice()
    {
        return $this->finalPrice;
    }
}
