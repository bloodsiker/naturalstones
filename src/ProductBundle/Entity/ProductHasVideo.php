<?php

namespace ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ProductHasVideo
 */
#[ORM\Table(name: 'product_product_has_video')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class ProductHasVideo
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
    #[ORM\ManyToOne(targetEntity: \ProductBundle\Entity\Product::class, inversedBy: 'productHasVideo')]
    protected $product;

    /**
     * @var \MediaBundle\Entity\MediaVideo
     */
    #[ORM\JoinColumn(name: 'video_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \MediaBundle\Entity\MediaVideo::class, fetch: 'EAGER')]
    protected $video;

    /**
     * @var int
     */
    #[ORM\Column(name: 'order_num', type: 'integer', nullable: false, options: ['default' => 1])]
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
     * @return ProductHasVideo
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
     * Set video.
     *
     * @return ProductHasVideo
     */
    public function setVideo(?\MediaBundle\Entity\MediaVideo $video = null)
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

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }
}
