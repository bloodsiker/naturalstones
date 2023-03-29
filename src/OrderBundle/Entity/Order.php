<?php

namespace OrderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Order
 *
 * @ORM\Entity(repositoryClass="OrderBundle\Entity\OrderRepository")
 * @ORM\Table(name="order_order")
 * @ORM\HasLifecycleCallbacks
 */
class Order
{
    const STATUS_NEW       = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_CANCEL    = 3;

    const TYPE_ORDER_CART = 1;
    const TYPE_ORDER_QUICK = 2;

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
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $fio;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $phone;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $messenger;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $instagram;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=false, options={"default": 0,00})
     */
    protected $totalSum;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=false, options={"default": 0,00})
     */
    protected $orderSum;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=false, options={"default": 0,00})
     */
    protected $discountPromo;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=false, options={"default": 0})
     */
    protected $type;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $callMe;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $comment;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $address;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\OrderHasItem",
     *     mappedBy="order", cascade={"all"}, orphanRemoval=true
     * )
     * @ORM\OrderBy({"orderNum" = "ASC"})
     */
    protected $orderHasItems;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->totalSum = 0;
        $this->orderSum = 0;
        $this->discountPromo = 0;
        $this->status = self::STATUS_NEW;
        $this->type = self::TYPE_ORDER_CART;
        $this->callMe = false;
        $this->createdAt = new \DateTime('now');

        $this->orderHasItems = new ArrayCollection();
    }

    /**
     * "String" representation of class
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->fio;
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
     * Get status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @return $this
     */
    public function setStatus($status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get fio
     *
     * @return string
     */
    public function getFio()
    {
        return $this->fio;
    }

    /**
     * Set fio
     *
     * @param string|null $fio
     *
     * @return $this
     */
    public function setFio(string $fio = null)
    {
        $this->fio = $fio;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email
     *
     * @param  string|null  $email
     *
     * @return $this
     */
    public function setEmail(string $email = null)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set phone
     *
     * @param  string|null  $phone
     *
     * @return $this
     */
    public function setPhone(string $phone = null)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get instagram
     *
     * @return string
     */
    public function getInstagram()
    {
        return $this->instagram;
    }

    /**
     * Set instagram
     *
     * @param  string|null  $instagram
     *
     * @return $this
     */
    public function setInstagram(string $instagram = null)
    {
        $this->instagram = $instagram;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param  string|null  $type
     *
     * @return $this
     */
    public function setType(string $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get callMe
     *
     * @return bool
     */
    public function getCallMe()
    {
        return $this->callMe;
    }

    /**
     * Set callMe
     *
     * @param  bool  $callMe
     *
     * @return $this
     */
    public function setCallMe(bool $callMe)
    {
        $this->callMe = $callMe;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return $this
     */
    public function setComment(string $comment = null)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return $this
     */
    public function setAddress(string $address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get messenger
     *
     * @return string
     */
    public function getMessenger()
    {
        return $this->messenger;
    }

    /**
     * Set messenger
     *
     * @param string $messenger
     *
     * @return $this
     */
    public function setMessenger(string $messenger = null)
    {
        $this->messenger = $messenger;

        return $this;
    }

    /**
     * Get totalSum
     *
     * @return int
     */
    public function getTotalSum()
    {
        return $this->totalSum;
    }

    /**
     * Set totalSum
     *
     * @param float $totalSum
     *
     * @return $this
     */
    public function setTotalSum(float $totalSum = 0)
    {
        $this->totalSum = $totalSum;

        return $this;
    }

    /**
     * Get orderSum
     *
     * @return int
     */
    public function getOrderSum()
    {
        return $this->orderSum;
    }

    /**
     * Set orderSum
     *
     * @param float $orderSum
     *
     * @return $this
     */
    public function setOrderSum(float $orderSum = 0)
    {
        $this->orderSum = $orderSum;

        return $this;
    }

    /**
     * Get discountPromo
     *
     * @return int
     */
    public function getDiscountPromo()
    {
        return $this->discountPromo;
    }

    /**
     * Set discountPromo
     *
     * @param float $discountPromo
     *
     * @return $this
     */
    public function setDiscountPromo(float $discountPromo = 0)
    {
        $this->discountPromo = $discountPromo;

        return $this;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return $this
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
     * Add orderHasItems.
     *
     * @param \OrderBundle\Entity\OrderHasItem $orderHasItems
     *
     * @return $this
     */
    public function addOrderHasItem(\OrderBundle\Entity\OrderHasItem $orderHasItems)
    {
        $orderHasItems->setOrder($this);
        $this->orderHasItems[] = $orderHasItems;

        return $this;
    }

    /**
     * Remove orderHasItems.
     *
     * @param \OrderBundle\Entity\OrderHasItem $orderHasItems
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeOrderHasItem(\OrderBundle\Entity\OrderHasItem $orderHasItems)
    {
        return $this->orderHasItems->removeElement($orderHasItems);
    }

    /**
     * Get orderHasItems.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrderHasItems()
    {
        return $this->orderHasItems;
    }

    /**
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_NEW       => 'new',
            self::STATUS_COMPLETED => 'completed',
            self::STATUS_CANCEL    => 'cancel',
        ];
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_ORDER_CART  => 'cart',
            self::TYPE_ORDER_QUICK => 'quick',
        ];
    }

    public static function getNameStatus($status)
    {
        $statuses = [
            self::STATUS_NEW       => 'order.fields.statuses.new',
            self::STATUS_COMPLETED => 'order.fields.statuses.completed',
            self::STATUS_CANCEL    => 'order.fields.statuses.cancel',
        ];

        return $statuses[$status];
    }
}
