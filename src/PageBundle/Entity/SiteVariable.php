<?php

namespace PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class SiteVariable
 *
 * @ORM\Entity(repositoryClass="PageBundle\Entity\SiteVariableRepository");
 * @ORM\Table(name="page_site_variable")
 * @ORM\HasLifecycleCallbacks
 */
class SiteVariable
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
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="PageBundle\Entity\SiteVariablePlacement")
     * @ORM\JoinColumn(name="placement_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $placement;

    /**
     * @var string
     *
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    protected $value;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $modifiedAt;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default": 1})
     */
    protected $isActive;

    /**
     * SiteVariable constructor.
     */
    public function __construct()
    {
        $this->isActive     = true;
        $this->createdAt    = $this->modifiedAt = new \DateTime('now');
    }

    /**
     * "String" representation of class
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->name;
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->modifiedAt = new \DateTime('now');
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return SiteVariable
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return SiteVariable
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return SiteVariable
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return SiteVariable
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set modifiedAt
     *
     * @param \DateTime $modifiedAt
     *
     * @return SiteVariable
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * Get modifiedAt
     *
     * @return \DateTime
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * Get placement
     *
     * @return int
     */
    public function getPlacement()
    {
        return $this->placement;
    }

    /**
     * Set placement
     *
     * @param int $placement
     *
     * @return $this
     */
    public function setPlacement($placement)
    {
        $this->placement = $placement;

        return $this;
    }
}
