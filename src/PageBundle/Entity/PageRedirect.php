<?php

namespace PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PageBundle\Model\RedirectInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="page_page_redirect", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="idx_unique_from_path", columns={"from_path"})
 * })
 */
class PageRedirect implements RedirectInterface
{
    public const TYPE_FULL = 1;
    public const TYPE_SEGMENT = 2;
    public const TYPE_REGEX = 3;
    public const TYPE_STARTS_WITH = 4;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     */
    protected ?string $fromPath = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected ?string $toPath = null;

    /**
     * @ORM\ManyToOne(targetEntity="\PageBundle\Entity\Page")
     * @ORM\JoinColumn(name="to_page_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected ?Page $toPage = null;

    /**
     * @ORM\Column(name="is_active", type="boolean", nullable=false)
     */
    protected bool $isActive = true;

    /**
     * @ORM\Column(type="smallint", columnDefinition="TINYINT(1) UNSIGNED DEFAULT 1 NOT NULL")
     */
    public ?int $type = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected ?string $help = null;

    public function __toString(): string
    {
        return (string) $this->getFromPath();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setFromPath(?string $fromPath): self
    {
        $this->fromPath = $fromPath;

        return $this;
    }

    public function getFromPath(): ?string
    {
        return $this->fromPath;
    }

    public function setToPath(?string $toPath): self
    {
        $this->toPath = $toPath;

        return $this;
    }

    public function getToPath(): ?string
    {
        return $this->toPath;
    }

    public function setToPage(?Page $toPage = null): self
    {
        $this->toPage = $toPage;

        return $this;
    }

    public function getToPage(): ?Page
    {
        return $this->toPage;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function setHelp(?string $help): self
    {
        $this->help = $help;

        return $this;
    }

    public function getHelp(): ?string
    {
        return $this->help;
    }

    public function getPageHost(): ?string
    {
        if (null === $this->toPage || null === $this->toPage->getSite()) {
            return null;
        }

        return $this->toPage->getSite()->getHost();
    }

    /**
     * @return array<int, string>
     */
    public static function getTypeList(): array
    {
        return [
            self::TYPE_FULL => 'full',
            self::TYPE_SEGMENT => 'segment',
            self::TYPE_REGEX => 'regex',
            self::TYPE_STARTS_WITH => 'starts_with',
        ];
    }
}