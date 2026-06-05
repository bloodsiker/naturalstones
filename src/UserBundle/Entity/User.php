<?php

namespace UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ProductBundle\Entity\Product;
use Sonata\IntlBundle\Timezone\TimezoneAwareInterface;
use Sonata\UserBundle\Entity\BaseUser;

/**
 * Class User
 *
 * @ORM\Entity
 * @ORM\Table(name="user_users")
 */
class User extends BaseUser implements TimezoneAwareInterface
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
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected ?string $firstname = null;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected ?string $lastname = null;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    protected ?string $phone = null;

    /**
     * @ORM\ManyToMany(targetEntity="ProductBundle\Entity\Product")
     * @ORM\JoinTable(name="user_users_favorite_product",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    private Collection $favorites;

    protected ?string $locale = null;

    protected ?string $timezone = null;

    public function __construct()
    {
        parent::__construct();
        $this->favorites = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Product $product): self
    {
        if (!$this->favorites->contains($product)) {
            $this->favorites->add($product);
        }

        return $this;
    }

    public function removeFavorite(Product $product): self
    {
        $this->favorites->removeElement($product);

        return $this;
    }

    public function hasFavorite(Product $product): bool
    {
        return $this->favorites->contains($product);
    }

    public function getLocale(): ?string
    {
        return $this->locale ?? 'uk';
    }

    public function setLocale(?string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone ?? 'Europe/Kyiv';
    }

    public function setTimezone(?string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }
}
