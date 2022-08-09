<?php

namespace ProductBundle\Entity;

use AppBundle\Traits\TranslatableProxyTrait;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Class ProductOptionLabel
 *
 * @ORM\Entity(repositoryClass="ProductBundle\Entity\CategoryRepository")
 * @ORM\Table(name="product_option_label")
 * @ORM\HasLifecycleCallbacks
 */
class ProductOptionLabel
{
    use ORMBehaviors\Translatable\Translatable;
    use TranslatableProxyTrait;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $isActive;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->isActive = true;
    }

    /**
     * "String" representation of class
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->translate()->getName();
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
     * @return $this
     */
    public function setIsActive(bool $isActive)
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
     * @return string
     */
    public function name()
    {
        return (string) $this->translate()->getName();
    }
}
