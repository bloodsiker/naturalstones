<?php

namespace OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ProductBundle\Entity\Product;
use ShareBundle\Entity\Colour;

#[ORM\Table(name: 'order_order_has_item')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class OrderHasItem
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \OrderBundle\Entity\Order::class, inversedBy: 'orderHasItems')]
    protected ?Order $order = null;

    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \ProductBundle\Entity\Product::class, fetch: 'EAGER')]
    protected ?Product $product = null;

    #[ORM\JoinColumn(name: 'colour_id', referencedColumnName: 'id', nullable: true)]
    #[ORM\ManyToOne(targetEntity: \ShareBundle\Entity\Colour::class, fetch: 'EAGER')]
    protected ?Colour $colour = null;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    protected ?string $options = null;

    #[ORM\Column(name: 'order_num', type: 'integer', nullable: false, options: ['default' => 1])]
    protected int $orderNum = 0;

    #[ORM\Column(type: 'integer', nullable: false)]
    protected int $quantity = 1;

    #[ORM\Column(type: 'float', nullable: false, options: ['default' => 0])]
    protected float $price = 0.0;

    #[ORM\Column(type: 'float', nullable: true)]
    protected ?float $discount = null;

    public function __toString(): string
    {
        return (string) $this->product;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setOrder(?Order $order = null): self
    {
        $this->order = $order;

        return $this;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setProduct(?Product $product = null): self
    {
        $this->product = $product;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setColour(?Colour $colour = null): self
    {
        $this->colour = $colour;

        return $this;
    }

    public function getColour(): ?Colour
    {
        return $this->colour;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getOptions(): ?string
    {
        return $this->options;
    }

    public function setOptions(?string $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function setOrderNum(int $orderNum): self
    {
        $this->orderNum = $orderNum;

        return $this;
    }

    public function getOrderNum(): int
    {
        return $this->orderNum;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDiscount(): ?float
    {
        return $this->discount;
    }

    public function setDiscount(?float $discount = null): self
    {
        $this->discount = $discount;

        return $this;
    }
}