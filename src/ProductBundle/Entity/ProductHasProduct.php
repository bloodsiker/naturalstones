<?php

namespace ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ProductHasProduct
 */
#[ORM\Table(name: 'product_product_has_product')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class ProductHasProduct
{
    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var Product
     */
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \ProductBundle\Entity\Product::class, inversedBy: 'productHasProduct')]
    protected $product;

    /**
     * @var Product
     */
    #[ORM\JoinColumn(name: 'product_set_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \ProductBundle\Entity\Product::class, fetch: 'EAGER')]
    protected $productSet;

    /**
     * @var int
     */
    #[ORM\Column(type: 'smallint', length: 6, nullable: false)]
    protected $quantity;

    /**
     * @var int
     */
    #[ORM\Column(name: 'order_num', type: 'integer', nullable: false, options: ['default' => 1])]
    protected $orderNum;

    protected $size;

    protected $price;

    protected $discount;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->quantity = 1;
        $this->orderNum = 0;
    }

    /**
     * "String" representation of class
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->product;
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
     * Set orderNum.
     *
     * @param int $orderNum
     *
     * @return ProductHasProduct
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
     * Set product.
     *
     * @return ProductHasProduct
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product.
     *
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set productSet.
     *
     * @return ProductHasProduct
     */
    public function setProductSet(?Product $productSet = null)
    {
        $this->productSet = $productSet;

        return $this;
    }

    /**
     * Get productSet.
     *
     * @return Product
     */
    public function getProductSet()
    {
        return $this->productSet;
    }

    /**
     * Set quantity.
     *
     * @param int $quantity
     *
     * @return ProductHasProduct
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity.
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return $this
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @return $this
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }
}
