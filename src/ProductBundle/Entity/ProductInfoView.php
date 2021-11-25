<?php

namespace ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class BookInfoView
 *
 * @ORM\Entity(repositoryClass="ProductBundle\Entity\ProductInfoViewRepository")
 * @ORM\Table(name="product_product_info_view")
 * @ORM\HasLifecycleCallbacks
 */
class ProductInfoView
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
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=true, onDelete="cascade")
     */
    protected $product;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", length=6, nullable=false)
     */
    protected $views;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", nullable=false)
     */
    protected $viewAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->views     = 0;
        $this->viewAt    = new \DateTime('now');
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

    /**
     * Set viewAt.
     *
     * @param \DateTime $viewAt
     *
     * @return $this
     */
    public function setViewAt($viewAt)
    {
        $this->viewAt = $viewAt;

        return $this;
    }

    /**
     * Get downloadAt.
     *
     * @return \DateTime
     */
    public function getViewAt()
    {
        return $this->viewAt;
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
     * Set views
     *
     * @param int $views
     *
     * @return $this
     */
    public function setViews(int $views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * @return int
     */
    public function doView()
    {
        return $this->views++;
    }
}
