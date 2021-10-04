<?php

namespace MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class AbstractMedia
 */
abstract class AbstractMedia
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $path;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    protected $mimeType;

    /**
     * @var string
     *
     * @ORM\Column(type="integer", nullable=true, options={"unsigned"=true})
     */
    protected $size;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $isActive;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $createdBy;

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
    protected $updatedAt;

    /**
     * Gallery constructor.
     */
    public function __construct()
    {
        $this->isActive  = true;
        $this->createdAt = new \DateTime('now');
    }

    /**
     * "String" representation of class
     * @return string
     */
    public function __toString()
    {
        return (string) '#'.$this->id.'-'.$this->description;
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
     * Set description
     *
     * @param string $description
     *
     * @return AbstractMedia
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return AbstractMedia
     */
    public function setPath($path = null)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set size.
     *
     * @param int|null $size
     *
     * @return AbstractMedia
     */
    public function setSize($size = null)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size.
     *
     * @return int|null
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return AbstractMedia
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
     * Set createdBy.
     *
     * @param \UserBundle\Entity\User|null $createdBy
     *
     * @return AbstractMedia
     */
    public function setCreatedBy(\UserBundle\Entity\User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy.
     *
     * @return \UserBundle\Entity\User|null
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set mimeType.
     *
     * @param string|null $mimeType
     *
     * @return AbstractMedia
     */
    public function setMimeType($mimeType = null)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * Get mimeType.
     *
     * @return string|null
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return AbstractMedia
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return AbstractMedia
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

}