<?php

namespace OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'order_promo_code')]
#[ORM\Entity(repositoryClass: \OrderBundle\Entity\PromoCodeRepository::class)]
#[ORM\HasLifecycleCallbacks]
class PromoCode
{
    public const TYPE_FIXED   = 'fixed';
    public const TYPE_PERCENT = 'percent';

    #[ORM\Id]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected int $id;

    #[ORM\Column(type: 'string', length: 64, unique: true, nullable: false)]
    protected string $code;

    #[ORM\Column(type: 'string', length: 10, nullable: false, options: ['default' => 'fixed'])]
    protected string $type = self::TYPE_FIXED;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: false, options: ['default' => '0.00'])]
    protected string $value = '0.00';

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?\DateTime $expiresAt = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    protected ?int $usageLimit = null;

    #[ORM\Column(type: 'integer', nullable: false, options: ['default' => 0])]
    protected int $usageCount = 0;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => true])]
    protected bool $isActive = true;

    #[ORM\Column(type: 'datetime', nullable: false)]
    protected \DateTime $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function __toString(): string
    {
        return $this->code ?? '';
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = strtoupper(trim($code));
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function getExpiresAt(): ?\DateTime
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTime $expiresAt): self
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    public function getUsageLimit(): ?int
    {
        return $this->usageLimit;
    }

    public function setUsageLimit(?int $usageLimit): self
    {
        $this->usageLimit = $usageLimit;
        return $this;
    }

    public function getUsageCount(): int
    {
        return $this->usageCount;
    }

    public function setUsageCount(int $usageCount): self
    {
        $this->usageCount = $usageCount;
        return $this;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function isValid(): bool
    {
        if (!$this->isActive) {
            return false;
        }

        if ($this->expiresAt !== null && $this->expiresAt < new \DateTime()) {
            return false;
        }

        if ($this->usageLimit !== null && $this->usageCount >= $this->usageLimit) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $total): float
    {
        if ($this->type === self::TYPE_PERCENT) {
            return round($total * (float) $this->value / 100, 2);
        }

        return min((float) $this->value, $total);
    }
}