<?php

namespace ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ProductHasVideo
 *
 * @ORM\Entity()
 * @ORM\Table(name="product_product_has_video")
 * @ORM\HasLifecycleCallbacks
 */
class ProductHasVideo
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
     * @var \MediaBundle\Entity\MediaVideo
     *
     * @ORM\ManyToOne(targetEntity="MediaBundle\Entity\MediaVideo", fetch="EAGER")
     * @ORM\JoinColumn(name="video_id", referencedColumnName="id", nullable=false)
     */
    protected $video;

    /**
     * @var int
     *
     * @ORM\Column(name="order_num", type="integer", nullable=false, options={"default": 1})
     */
    protected $orderNum;

    protected $path;

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
     * @return ProductHasVideo
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
     * @return ProductHasVideo
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
     * Set video.
     *
     * @param \MediaBundle\Entity\MediaVideo $image
     *
     * @return ProductHasVideo
     */
    public function setVideo(\MediaBundle\Entity\MediaVideo $video = null)
    {
        $this->video = $video;

        return $this;
    }

    /**
     * Get image.
     *
     * @return \MediaBundle\Entity\MediaVideo
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param  mixed  $path
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }
}
