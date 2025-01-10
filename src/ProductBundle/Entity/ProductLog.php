<?php

namespace ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ProductLog
 *
 * @ORM\Entity()
 * @ORM\Table(name="product_product_log")
 * @ORM\HasLifecycleCallbacks
 */
class ProductLog
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
     * @ORM\ManyToOne(targetEntity="ProductBundle\Entity\Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="cascade")
     */
    protected $product;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    protected $newPrice;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    protected $oldPrice;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
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
     * Set product
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
     * Get product
     *
     * @return \ProductBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    public function getNewPrice()
    {
        return $this->newPrice;
    }

    public function setNewPrice(float $newPrice)
    {
        $this->newPrice = $newPrice;

        return $this;
    }

    public function getOldPrice()
    {
        return $this->oldPrice;
    }

    public function setOldPrice(float $oldPrice)
    {
        $this->oldPrice = $oldPrice;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
