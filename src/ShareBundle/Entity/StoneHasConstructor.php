<?php

namespace ShareBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class StoneHasConstructor
 */
#[ORM\Table(name: 'share_stone_has_constructor')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class StoneHasConstructor
{
    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var Stone
     */
    #[ORM\JoinColumn(name: 'stone_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \ShareBundle\Entity\Stone::class, inversedBy: 'stoneHasConstructor')]
    protected $stone;

    /**
     * @var \MediaBundle\Entity\MediaImage
     */
    #[ORM\JoinColumn(name: 'image_id', referencedColumnName: 'id', nullable: true)]
    #[ORM\ManyToOne(targetEntity: \MediaBundle\Entity\MediaImage::class, fetch: 'EAGER')]
    protected $image;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 20, nullable: false)]
    protected $size;

    /**
     * @var int
     */
    #[ORM\Column(name: 'order_num', type: 'integer', nullable: false, options: ['default' => 1])]
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
        return (string) $this->stone;
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
     * Set stone.
     *
     * @return $this
     */
    public function setStone(Stone $stone)
    {
        $this->stone = $stone;

        return $this;
    }

    /**
     * Get stone.
     *
     * @return Stone
     */
    public function getStone()
    {
        return $this->stone;
    }

    /**
     * Set image.
     *
     * @return $this
     */
    public function setImage(?\MediaBundle\Entity\MediaImage $image = null)
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

    /**
     * Set size
     *
     * @return $this
     */
    public function setSize(string $size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }
}
