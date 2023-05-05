<?php

namespace WheelSpinBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class WheelSpin
 *
 * @ORM\Entity(repositoryClass="WheelSpinBundle\Entity\WheelSpinRepository")
 * @ORM\Table(name="wheel_spin")
 * @ORM\HasLifecycleCallbacks
 */
class WheelSpin
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
     * @var float
     *
     * @ORM\Column(type="float", nullable=false, options={"default": 0,00})
     */
    protected $minSum;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=false, options={"default": 0,00})
     */
    protected $maxSum;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $isActive;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="WheelSpinBundle\Entity\WheelSpinHasOption",
     *     mappedBy="wheelSpin", cascade={"all"}, orphanRemoval=true
     * )
     * @ORM\OrderBy({"label" = "ASC"})
     */
    protected $wheelSpinHasOption;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->isActive = true;

        $this->wheelSpinHasOption = new ArrayCollection();
    }

    /**
     * "String" representation of class
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s - %s', $this->minSum, $this->maxSum);
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
     * @return float
     */
    public function getMinSum()
    {
        return $this->minSum;
    }

    /**
     * @param  float  $minSum
     *
     * @return $this
     */
    public function setMinSum(float $minSum)
    {
        $this->minSum = $minSum;

        return $this;
    }

    /**
     * @return float
     */
    public function getMaxSum()
    {
        return $this->maxSum;
    }

    /**
     * @param  float  $maxSum
     *
     * @return $this
     */
    public function setMaxSum(float $maxSum)
    {
        $this->maxSum = $maxSum;

        return $this;
    }

    /**
     * Set isActive
     *
     * @param bool $isActive
     *
     * @return $this
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Add wheelSpinHasOption.
     *
     * @param WheelSpinHasOption $wheelSpinHasOption
     *
     * @return $this
     */
    public function addWheelSpinHasOption(WheelSpinHasOption $wheelSpinHasOption)
    {
        $wheelSpinHasOption->setWheelSpin($this);
        $this->wheelSpinHasOption[] = $wheelSpinHasOption;

        return $this;
    }

    /**
     * Remove wheelSpinHasOption.
     *
     * @param WheelSpinHasOption $wheelSpinHasOption
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeWheelSpinHasOption(WheelSpinHasOption $wheelSpinHasOption)
    {
        return $this->wheelSpinHasOption->removeElement($wheelSpinHasOption);
    }

    /**
     * Get wheelSpinHasOption.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWheelSpinHasOption()
    {
        return $this->wheelSpinHasOption;
    }
}
