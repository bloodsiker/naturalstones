<?php

namespace PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PageBundle\Model\RedirectInterface;

/**
 * Class PageRedirect
 *
 * @ORM\Entity
 * @ORM\Table(name="page_page_redirect", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="idx_unique_from_path", columns={"from_path"})
 * })
 */
class PageRedirect implements RedirectInterface
{
    const TYPE_FULL         = 1;
    const TYPE_SEGMENT      = 2;
    const TYPE_REGEX        = 3;
    const TYPE_STARTS_WITH  = 4;

    /**
     * @var int $id
     *
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     */
    protected $fromPath;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $toPath;

    /**
     * @var \PageBundle\Entity\Page
     *
     * @ORM\ManyToOne(targetEntity="\PageBundle\Entity\Page")
     * @ORM\JoinColumn(name="to_page_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $toPage;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=false)
     */
    protected $isActive;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", columnDefinition="TINYINT(1) UNSIGNED DEFAULT 1 NOT NULL")
     */
    public $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $help;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->isActive = true;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getFromPath();
    }

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fromPath
     *
     * @param string $fromPath
     *
     * @return PageRedirect
     */
    public function setFromPath($fromPath)
    {
        $this->fromPath = $fromPath;

        return $this;
    }

    /**
     * Get fromPath
     *
     * @return string
     */
    public function getFromPath()
    {
        return $this->fromPath;
    }

    /**
     * Set toPath
     *
     * @param string $toPath
     *
     * @return PageRedirect
     */
    public function setToPath($toPath)
    {
        $this->toPath = $toPath;

        return $this;
    }

    /**
     * Get redirectTo
     *
     * @return string
     */
    public function getToPath()
    {
        return $this->toPath;
    }

    /**
     * Set toPage
     *
     * @param \PageBundle\Entity\Page $toPage
     *
     * @return PageRedirect
     */
    public function setToPage(\PageBundle\Entity\Page $toPage = null)
    {
        $this->toPage = $toPage;

        return $this;
    }

    /**
     * Get redirectToPage
     *
     * @return Page
     */
    public function getToPage()
    {
        return $this->toPage;
    }

    /**
     * Set isActive
     *
     * @param bool $isActive
     *
     * @return PageRedirect
     */
    public function setIsActive(bool $isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param int $type
     *
     * @return PageRedirect
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set help
     *
     * @param string $help
     *
     * @return PageRedirect
     */
    public function setHelp($help)
    {
        $this->help = $help;

        return $this;
    }

    /**
     * Get help
     *
     * @return string
     */
    public function getHelp()
    {
        return $this->help;
    }

    /**
     * @return array
     */
    public static function getTypeList()
    {
        return [
            self::TYPE_FULL         => 'full',
            self::TYPE_SEGMENT      => 'segment',
            self::TYPE_REGEX        => 'regex',
            self::TYPE_STARTS_WITH  => 'starts_with',
        ];
    }
}
