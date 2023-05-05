<?php

namespace WheelSpinBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class WheelSpinHasOption
 *
 * @ORM\Entity()
 * @ORM\Table(name="wheel_spin_has_option")
 * @ORM\HasLifecycleCallbacks
 */
class WheelSpinHasOption
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
     * @var WheelSpin
     *
     * @ORM\ManyToOne(targetEntity="WheelSpinBundle\Entity\WheelSpin", inversedBy="wheelSpinHasOption")
     * @ORM\JoinColumn(name="wheel_spin_id", referencedColumnName="id", nullable=false)
     */
    protected $wheelSpin;

    /**
     * @var WheelSpinOption
     *
     * @ORM\ManyToOne(targetEntity="WheelSpinBundle\Entity\WheelSpinOption", fetch="EAGER")
     * @ORM\JoinColumn(name="wheel_spin_option_id", referencedColumnName="id", nullable=true)
     */
    protected $wheelSpinOption;

    /**
     * @var float
     *
     * @ORM\Column(type="integer", nullable=false, options={"default": 0})
     */
    protected $valuation;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=false, options={"default": 0,00})
     */
    protected $percent;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $colour;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $degrees;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $label;

    /**
     * @var int
     *
     * @ORM\Column(name="order_num", type="integer", nullable=false, options={"default": 1})
     */
    protected $orderNum;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->valuation = 0;
        $this->percent = 0;
        $this->orderNum = 0;
    }

    /**
     * "String" representation of class
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->wheelSpin;
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
     * @return WheelSpin
     */
    public function getWheelSpin()
    {
        return $this->wheelSpin;
    }

    /**
     * @param  WheelSpin  $wheelSpin
     *
     * @return $this
     */
    public function setWheelSpin(WheelSpin $wheelSpin)
    {
        $this->wheelSpin = $wheelSpin;

        return $this;
    }

    public function getWheelSpinOption()
    {
        return $this->wheelSpinOption;
    }

    public function setWheelSpinOption(WheelSpinOption $wheelSpinOption)
    {
        $this->wheelSpinOption = $wheelSpinOption;

        return $this;
    }

    /**
     * @return float
     */
    public function getValuation(): float
    {
        return $this->valuation;
    }

    /**
     * @param  float  $valuation
     *
     * @return $this
     */
    public function setValuation($valuation)
    {
        $this->valuation = $valuation;

        return $this;
    }

    /**
     * @return float
     */
    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * @param  float  $percent
     *
     * @return $this
     */
    public function setPercent($percent = 0)
    {
        $this->percent = $percent;

        return $this;
    }

    /**
     * @return string
     */
    public function getColour()
    {
        return $this->colour;
    }

    /**
     * @param  string  $colour
     *
     * @return $this
     */
    public function setColour(string $colour)
    {
        $this->colour = $colour;

        return $this;
    }

    /**
     * @return string
     */
    public function getDegrees()
    {
        return $this->degrees;
    }

    /**
     * @param  string  $degrees
     *
     * @return $this
     */
    public function setDegrees(string $degrees)
    {
        $this->degrees = $degrees;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param  string  $label
     *
     * @return $this
     */
    public function setLabel(string $label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Set orderNum.
     *
     * @param int $orderNum
     *
     * @return $this
     */
    public function setOrderNum($orderNum)
    {
        $this->orderNum = $orderNum;

        return $this;
    }

    /**
     * Get orderNum.
     *
     * @return int
     */
    public function getOrderNum()
    {
        return $this->orderNum;
    }
}
