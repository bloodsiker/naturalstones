<?php

namespace OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class OrderHasItem
 *
 * @ORM\Entity()
 * @ORM\Table(name="order_order_has_item")
 * @ORM\HasLifecycleCallbacks
 */
class OrderHasItem
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
     * @var \OrderBundle\Entity\Order
     *
     * @ORM\ManyToOne(targetEntity="OrderBundle\Entity\Order", inversedBy="orderHasItems")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", nullable=false)
     */
    protected $order;

    /**
     * @var \ProductBundle\Entity\Product
     *
     * @ORM\ManyToOne(targetEntity="ProductBundle\Entity\Product", fetch="EAGER")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=false)
     */
    protected $product;

    /**
     * @var \ProductBundle\Entity\Product
     *
     * @ORM\ManyToOne(targetEntity="ShareBundle\Entity\Colour", fetch="EAGER")
     * @ORM\JoinColumn(name="colour_id", referencedColumnName="id", nullable=true)
     */
    protected $colour;

    /**
     * @var int
     *
     * @ORM\Column(name="order_num", type="integer", nullable=false, options={"default": 1})
     */
    protected $orderNum;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $quantity;

    /**
     * @var int
     *
     * @ORM\Column(type="float", nullable=false, options={"default": 0})
     */
    protected $price;

    /**
     * @var int
     *
     * @ORM\Column(type="float", nullable=true)
     */
    protected $discount;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->orderNum = 0;
        $this->quantity = 1;
        $this->price = 0;
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
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set order.
     *
     * @param \OrderBundle\Entity\Order $order
     *
     * @return $this
     */
    public function setOrder(\OrderBundle\Entity\Order $order = null)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order.
     *
     * @return \OrderBundle\Entity\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set product.
     *
     * @param \ProductBundle\Entity\Product $product
     *
     * @return $this
     */
    public function setProduct(\ProductBundle\Entity\Product $product = null)
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
     * Set colour.
     *
     * @param \ShareBundle\Entity\Colour $colour
     *
     * @return $this
     */
    public function setColour(\ShareBundle\Entity\Colour $colour = null)
    {
        $this->colour = $colour;

        return $this;
    }

    /**
     * Get colour.
     *
     * @return \ShareBundle\Entity\Colour
     */
    public function getColour()
    {
        return $this->colour;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set quantity
     *
     * @param int $quantity
     *
     * @return $this
     */
    public function setQuantity(int $quantity)
    {
        $this->quantity = $quantity;

        return $this;
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
     * Get price
     *
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return $this
     */
    public function setPrice(float $price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get discount
     *
     * @return int
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set discount
     *
     * @param float|null $discount
     *
     * @return $this
     */
    public function setDiscount(float $discount = null)
    {
        $this->discount = $discount;

        return $this;
    }
}
