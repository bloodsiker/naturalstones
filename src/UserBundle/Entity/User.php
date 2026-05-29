<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\IntlBundle\Timezone\TimezoneAwareInterface;
use Sonata\UserBundle\Entity\BaseUser as BaseUser;

/**
 * Class User
 * @package UserBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="user_users")
 */
class User extends BaseUser implements TimezoneAwareInterface
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    protected ?string $locale = null;

    protected ?string $timezone = null;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
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
