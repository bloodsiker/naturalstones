<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="UserBundle\Entity\UserDeliveryAddressRepository")
 * @ORM\Table(
 *     name="user_users_delivery_address",
 *     indexes={
 *         @ORM\Index(name="idx_user_delivery_address_user", columns={"user_id"}),
 *         @ORM\Index(name="idx_user_delivery_address_default", columns={"is_default"})
 *     }
 * )
 * @ORM\HasLifecycleCallbacks
 */
class UserDeliveryAddress
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected ?User $user = null;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    protected ?string $title = null;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    protected ?string $address = null;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    protected bool $isDefault = false;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected \DateTime $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
    }

    public function __toString(): string
    {
        return (string) ($this->title ?: $this->address);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getIsDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): self
    {
        $this->isDefault = $isDefault;

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
}