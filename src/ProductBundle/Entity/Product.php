<?php

namespace ProductBundle\Entity;

use AppBundle\Traits\TranslatableProxyTrait;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Class Product
 */
#[ORM\Table(name: 'product_product')]
#[ORM\Entity(repositoryClass: \ProductBundle\Entity\ProductRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Product implements TranslatableInterface
{
    use ORMBehaviors\Translatable\TranslatableTrait;
    use TranslatableProxyTrait;

    public const WHO_MAN = 'man';
    public const WHO_WOMAN = 'woman';

    public const TYPE_LETTERS = 1;
    public const TYPE_INSERTS = 2;
    public const TYPE_PENDANTS = 3;
    public const TYPE_BRACELET = 4;
    public const TYPE_RINGS = 5;
    public const TYPE_NECKLACE = 6;
    public const TYPE_EARRING = 7;
    public const TYPE_MONEY = 8;

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
    #[ORM\ManyToOne(targetEntity: \ProductBundle\Entity\Category::class, inversedBy: 'products')]
    protected $category;

    /**
     * @var ProductOptionLabel
     */
    #[ORM\JoinColumn(name: 'option_label_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: \ProductBundle\Entity\ProductOptionLabel::class)]
    protected $optionLabel;

    /**
     * @var \ShareBundle\Entity\Size
     */
    #[ORM\JoinColumn(name: 'size_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: \ShareBundle\Entity\Size::class)]
    protected $size;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    protected $slug;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    protected $instagram_link;

    /**
     * @var float
     */
    #[ORM\Column(type: 'float', nullable: true)]
    protected $price;

    /**
     * @var float
     */
    #[ORM\Column(type: 'float', nullable: true)]
    protected $discount;

    /**
     * @var float
     */
    #[ORM\Column(type: 'float', nullable: true)]
    protected $percent;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean', nullable: false)]
    protected $isActive;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean', nullable: false)]
    protected $isMan;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean', nullable: false)]
    protected $isWoman;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean', nullable: false)]
    protected $isAvailable;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean', nullable: false)]
    protected $isMainProduct;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer', nullable: false)]
    protected $views;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer', nullable: false)]
    protected $productGroup;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'smallint', nullable: false)]
    protected $optionType;

    /**
     * @var ArrayCollection
     */
    #[ORM\JoinTable(name: 'product_product_colours')]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'colour_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: \ShareBundle\Entity\Colour::class, inversedBy: 'products')]
    protected $colours;

    /**
     * @var ArrayCollection
     */
    #[ORM\JoinTable(name: 'product_product_metals')]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'metal_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: \ShareBundle\Entity\Metal::class)]
    protected $metals;

    /**
     * @var ArrayCollection
     */
    #[ORM\JoinTable(name: 'product_product_tags')]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'tag_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: \ShareBundle\Entity\Tag::class, inversedBy: 'products')]
    protected $tags;

    /**
     * @var ArrayCollection
     */
    #[ORM\JoinTable(name: 'product_product_stones')]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'stone_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: \ShareBundle\Entity\Stone::class)]
    protected $stones;

    /**
     * @var ArrayCollection
     */
    #[ORM\OneToMany(targetEntity: \ProductBundle\Entity\ProductHasImage::class, mappedBy: 'product', cascade: ['all'], orphanRemoval: true)]
    #[ORM\OrderBy(['orderNum' => 'ASC'])]
    protected $productHasImage;

    /**
     * @var ArrayCollection
     */
    #[ORM\OneToMany(targetEntity: \ProductBundle\Entity\ProductHasVideo::class, mappedBy: 'product', cascade: ['all'], orphanRemoval: true)]
    #[ORM\OrderBy(['orderNum' => 'ASC'])]
    protected $productHasVideo;

    /**
     * @var ArrayCollection
     */
    #[ORM\OneToMany(targetEntity: \ProductBundle\Entity\ProductHasProduct::class, mappedBy: 'product', cascade: ['all'], orphanRemoval: true)]
    #[ORM\OrderBy(['orderNum' => 'ASC'])]
    protected $productHasProduct;

    /**
     * @var ArrayCollection
     */
    #[ORM\OneToMany(targetEntity: \ProductBundle\Entity\ProductHasOption::class, mappedBy: 'product', cascade: ['all'], orphanRemoval: true)]
    #[ORM\OrderBy(['orderNum' => 'ASC'])]
    protected $productHasOption;

    /**
     * @var ArrayCollection
     */
    #[ORM\OneToMany(targetEntity: \ProductBundle\Entity\ProductHasOptionMetal::class, mappedBy: 'product', cascade: ['all'], orphanRemoval: true)]
    #[ORM\OrderBy(['orderNum' => 'ASC'])]
    protected $productHasOptionMetal;

    /**
     * @var ArrayCollection
     */
    #[ORM\OneToMany(targetEntity: \ProductBundle\Entity\ProductHasOptionColour::class, mappedBy: 'product', cascade: ['all'], orphanRemoval: true)]
    #[ORM\OrderBy(['orderNum' => 'ASC'])]
    protected $productHasOptionColour;

    /**
     * @var int
     */
    #[ORM\Column(name: 'order_num', type: 'integer', nullable: false, options: ['default' => 1])]
    protected $orderNum;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    protected $telegramMessageId;

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
     * @var float
     */
    protected $finalPrice;

    /**
     * @var float
     */
    private $oldPrice;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->size = null;
        $this->isActive = true;
        $this->isAvailable = true;
        $this->isMan = false;
        $this->isWoman = true;
        $this->isMainProduct = true;
        $this->views = 0;
        $this->price = 0;
        $this->discount = 0;
        $this->percent = 0;
        $this->finalPrice = 0;
        $this->orderNum = 1;
        $this->createdAt = new \DateTime('now');

        $this->colours = new ArrayCollection();
        $this->metals = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->stones = new ArrayCollection();
        $this->productHasImage = new ArrayCollection();
        $this->productHasVideo = new ArrayCollection();
        $this->productHasProduct = new ArrayCollection();
        $this->productHasOption = new ArrayCollection();
        $this->productHasOptionMetal = new ArrayCollection();
        $this->productHasOptionColour = new ArrayCollection();
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

    #[ORM\PrePersist]
    public function prePersist()
    {
        if (is_null($this->slug)) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->translate('ru')->getName());
        }
    }

    #[ORM\PreUpdate]
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
     * @return int
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
     * @return Product
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
     * Set category
     *
     * @return Product
     */
    public function setCategory(?Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set optionLabel
     *
     * @return Product
     */
    public function setOptionLabel(?ProductOptionLabel $optionLabel = null)
    {
        $this->optionLabel = $optionLabel;

        return $this;
    }

    /**
     * Get optionLabel
     *
     * @return ProductOptionLabel
     */
    public function getOptionLabel()
    {
        return $this->optionLabel;
    }

    /**
     * Set size
     *
     * @return Product
     */
    public function setSize(?\ShareBundle\Entity\Size $size = null)
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
     * Get description
     *
     * @return string
     */
    public function description()
    {
        return $this->translate()->getDescription();
    }

    /**
     * Set isActive
     *
     * @param bool $isActive
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
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set isAvailable
     *
     * @param bool $isAvailable
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
     * @return bool
     */
    public function getIsAvailable()
    {
        return $this->isAvailable;
    }

    /**
     * Set isMan
     *
     * @param bool $isMan
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
     * @return bool
     */
    public function getIsMan()
    {
        return $this->isMan;
    }

    /**
     * Set isWoman
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
     * @return bool
     */
    public function getIsWoman()
    {
        return $this->isWoman;
    }

    /**
     * Set isMainProduct
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
     * @return bool
     */
    public function getIsMainProduct()
    {
        return $this->isMainProduct;
    }

    /**
     * Set views
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
     * @return int
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Set productGroup
     *
     * @return Product
     */
    public function setProductGroup(?int $productGroup = null)
    {
        $this->productGroup = $productGroup;

        return $this;
    }

    /**
     * Get productGroup
     *
     * @return int
     */
    public function getProductGroup()
    {
        return $this->productGroup;
    }

    /**
     * Add colours
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
     * @return Product
     */
    public function addMetal(\ShareBundle\Entity\Metal $metal)
    {
        $this->metals[] = $metal;

        return $this;
    }

    /**
     * Remove colours
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
     * @return Product
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

    /**
     * Add stones
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
        if ($this->price !== $price) {
            $this->oldPrice = $this->price;
        }

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
     * @return int
     */
    public function getTelegramMessageId()
    {
        return $this->telegramMessageId;
    }

    /**
     * @return $this
     */
    public function setTelegramMessageId($telegramMessageId)
    {
        $this->telegramMessageId = $telegramMessageId;

        return $this;
    }

    /**
     * Add ProductHasImage.
     *
     * @return Product
     */
    public function addProductHasImage(ProductHasImage $productHasImage)
    {
        $productHasImage->setProduct($this);
        $this->productHasImage[] = $productHasImage;

        return $this;
    }

    /**
     * Remove ProductHasImage.
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeProductHasImage(ProductHasImage $productHasImage)
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
     * Add ProductHasVideo.
     *
     * @return Product
     */
    public function addProductHasVideo(ProductHasVideo $productHasVideo)
    {
        $productHasVideo->setProduct($this);
        $this->productHasVideo[] = $productHasVideo;

        return $this;
    }

    /**
     * Remove ProductHasVideo.
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeProductHasVideo(ProductHasVideo $productHasVideo)
    {
        return $this->productHasVideo->removeElement($productHasVideo);
    }

    /**
     * Get ProductHasVideo.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductHasVideo()
    {
        return $this->productHasVideo;
    }

    /**
     * Add productHasProduct.
     *
     * @return Product
     */
    public function addProductHasProduct(ProductHasProduct $productHasProduct)
    {
        $productHasProduct->setProduct($this);
        $this->productHasProduct[] = $productHasProduct;

        return $this;
    }

    /**
     * Remove productHasProduct.
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeProductHasProduct(ProductHasProduct $productHasProduct)
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
     * @return Product
     */
    public function addProductHasOption(ProductHasOption $productHasOption)
    {
        $productHasOption->setProduct($this);
        $this->productHasOption[] = $productHasOption;

        return $this;
    }

    /**
     * Remove productHasOption.
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeProductHasOption(ProductHasOption $productHasOption)
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
     * @return Product
     */
    public function addProductHasOptionMetal(ProductHasOptionMetal $productHasOptionMetal)
    {
        $productHasOptionMetal->setProduct($this);
        $this->productHasOptionMetal[] = $productHasOptionMetal;

        return $this;
    }

    /**
     * Remove productHasOptionMetal.
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeProductHasOptionMetal(ProductHasOptionMetal $productHasOptionMetal)
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
     * @return Product
     */
    public function addProductHasOptionColour(ProductHasOptionColour $productHasOptionColour)
    {
        $productHasOptionColour->setProduct($this);
        $this->productHasOptionColour[] = $productHasOptionColour;

        return $this;
    }

    /**
     * Remove productHasOptionColour.
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeProductHasOptionColour(ProductHasOptionColour $productHasOptionColour)
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
        self::WHO_MAN => 'frontend.breadcrumb.man',
        self::WHO_WOMAN => 'frontend.breadcrumb.woman',
    ];

    public static function getTypes()
    {
        return [
            self::TYPE_LETTERS => 'letters',
            self::TYPE_INSERTS => 'inserts',
            self::TYPE_PENDANTS => 'pendants',
            self::TYPE_BRACELET => 'bracelets',
            self::TYPE_RINGS => 'rings',
            self::TYPE_NECKLACE => 'necklaces',
            self::TYPE_EARRING => 'earrings',
            self::TYPE_MONEY => 'money',
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

    public function getOldPrice(): ?float
    {
        return $this->oldPrice;
    }

    public function setOldPrice(?float $oldPrice): void
    {
        $this->oldPrice = $oldPrice;
    }
}
