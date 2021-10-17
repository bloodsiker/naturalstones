<?php

namespace ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ProductHasImage
 *
 * @ORM\Entity()
 * @ORM\Table(name="product_product_has_image")
 * @ORM\HasLifecycleCallbacks
 */
class ProductHasImage
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
     * @ORM\ManyToOne(targetEntity="ProductBundle\Entity\Product", inversedBy="productHasImage")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=false)
     */
    protected $product;

    /**
     * @var \MediaBundle\Entity\MediaImage
     *
     * @ORM\ManyToOne(targetEntity="MediaBundle\Entity\MediaImage", fetch="EAGER")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", nullable=false)
     */
    protected $image;

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
     * @return ProductHasImage
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
     * @return ProductHasImage
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
     * Set image.
     *
     * @param \MediaBundle\Entity\MediaImage $image
     *
     * @return ProductHasImage
     */
    public function setImage(\MediaBundle\Entity\MediaImage $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image.
     *
     * @return \MediaBundle\Entity\MediaImage
     */
    public function getImage()
    {
        return $this->image;
    }
}
