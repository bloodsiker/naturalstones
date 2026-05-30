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
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $user;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     */
    protected $address;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    protected $isDefault;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $createdAt;

    public function __construct()
    {
        $this->isDefault = false;
        $this->createdAt = new \DateTime('now');
    }

    public function __toString()
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
        return (bool) $this->isDefault;
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
