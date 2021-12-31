<?php

namespace ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ProductHasProduct
 *
 * @ORM\Entity()
 * @ORM\Table(name="product_product_has_product")
 * @ORM\HasLifecycleCallbacks
 */
class ProductHasProduct
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
     * @var \ProductBundle\Entity\Product
     *
     * @ORM\ManyToOne(targetEntity="ProductBundle\Entity\Product", inversedBy="productHasProduct")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=false)
     */
    protected $product;

    /**
     * @var \ProductBundle\Entity\Product
     *
     * @ORM\ManyToOne(targetEntity="ProductBundle\Entity\Product", fetch="EAGER")
     * @ORM\JoinColumn(name="product_set_id", referencedColumnName="id", nullable=false)
     */
    protected $productSet;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", length=6, nullable=false)
     */
    protected $quantity;

    /**
     * @var int
     *
     * @ORM\Column(name="order_num", type="integer", nullable=false, options={"default": 1})
     */
    protected $orderNum;

    /**
     * @var
     */
    protected $size;

    /**
     * @var
     */
    protected $price;

    /**
     * @var
     */
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
        return (string) $this->product ;
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
     * @param \ProductBundle\Entity\Product $product
     *
     * @return ProductHasProduct
     */
    public function setProduct(\ProductBundle\Entity\Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product.
     *
     * @return \ProductBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set productSet.
     *
     * @param \ProductBundle\Entity\Product $productSet
     *
     * @return ProductHasProduct
     */
    public function setProductSet(\ProductBundle\Entity\Product $productSet = null)
    {
        $this->productSet = $productSet;

        return $this;
    }

    /**
     * Get productSet.
     *
     * @return \ProductBundle\Entity\Product
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

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param $size
     *
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param $price
     *
     * @return $this
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param $discount
     *
     * @return $this
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }
}
