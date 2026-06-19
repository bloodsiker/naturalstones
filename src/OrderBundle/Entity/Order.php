<?php

namespace OrderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;
use WheelSpinBundle\Entity\WheelSpinOption;

/**
 * Class Order
 */
#[ORM\Table(name: 'order_order')]
#[ORM\Entity(repositoryClass: \OrderBundle\Entity\OrderRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Order
{
    public const STATUS_NEW = 1;
    public const STATUS_COMPLETED = 2;
    public const STATUS_CANCEL = 3;

    public const TYPE_ORDER_CART = 1;
    public const TYPE_ORDER_QUICK = 2;

    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    protected $fio;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    protected $email;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    protected $phone;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    protected $messenger;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected $instagram;

    /**
     * @var float
     */
    #[ORM\Column(type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => '0.00'])]
    protected $totalSum;

    /**
     * @var float
     */
    #[ORM\Column(type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => '0.00'])]
    protected $orderSum;

    /**
     * @var float
     */
    #[ORM\Column(type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => '0.00'])]
    protected $discountPromo;

    /**
     * @var int
     */
    #[ORM\Column(type: 'smallint', nullable: false, options: ['default' => 0])]
    protected $type;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean', nullable: false)]
    protected $callMe;

    /**
     * @var string
     */
    #[ORM\Column(type: 'text', nullable: true)]
    protected $comment;

    /**
     * @var string
     */
    #[ORM\Column(type: 'text', nullable: true)]
    protected $address;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer', nullable: false)]
    protected $status;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    protected $secret;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean', nullable: false)]
    protected $isSpin;

    /**
     * @var \DateTime
     */
    #[ORM\Column(type: 'datetime', nullable: false)]
    protected $createdAt;

    /**
     * @var WheelSpinOption
     */
    #[ORM\JoinColumn(name: 'wheel_spin_option_id', referencedColumnName: 'id', nullable: true)]
    #[ORM\ManyToOne(targetEntity: \WheelSpinBundle\Entity\WheelSpinOption::class, fetch: 'EAGER')]
    protected $wheelSpinOption;

    /**
     * @var User|null
     */
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: \UserBundle\Entity\User::class)]
    protected $user;

    /**
     * @var PromoCode|null
     */
    #[ORM\JoinColumn(name: 'promo_code_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: \OrderBundle\Entity\PromoCode::class)]
    protected $promoCode = null;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'telegram_chat_id', type: 'string', length: 100, nullable: true)]
    protected $telegramChatId;

    /**
     * @var int|null
     */
    #[ORM\Column(name: 'telegram_message_id', type: 'integer', nullable: true)]
    protected $telegramMessageId;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected $spinPrize;

    /**
     * @var ArrayCollection
     */
    #[ORM\OneToMany(targetEntity: \OrderBundle\Entity\OrderHasItem::class, mappedBy: 'order', cascade: ['all'], orphanRemoval: true)]
    #[ORM\OrderBy(['orderNum' => 'ASC'])]
    protected $orderHasItems;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->secret = bin2hex(random_bytes(20));
        $this->totalSum = 0;
        $this->orderSum = 0;
        $this->discountPromo = 0;
        $this->status = self::STATUS_NEW;
        $this->type = self::TYPE_ORDER_CART;
        $this->callMe = false;
        $this->isSpin = false;
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @return $this
     */
    public function setFio(?string $fio = null)
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
     * @return $this
     */
    public function setEmail(?string $email = null)
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
     * @return $this
     */
    public function setPhone(?string $phone = null)
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
     * @return $this
     */
    public function setInstagram(?string $instagram = null)
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
     * @return $this
     */
    public function setType(?string $type = null)
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
     * @return $this
     */
    public function setComment(?string $comment = null)
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
     * @return $this
     */
    public function setAddress(?string $address = null)
    {
        $this->address = $address;

        return $this;
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
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @return $this
     */
    public function setSecret(string $secret)
    {
        $this->secret = $secret;

        return $this;
    }

    public function getIsSpin(): bool
    {
        return $this->isSpin;
    }

    /**
     * @return $this
     */
    public function setIsSpin(bool $isSpin)
    {
        $this->isSpin = $isSpin;

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
     * @return $this
     */
    public function setMessenger(?string $messenger = null)
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
     * @return $this
     */
    public function setDiscountPromo(float $discountPromo = 0)
    {
        $this->discountPromo = $discountPromo;

        return $this;
    }

    public function getPromoCode(): ?PromoCode
    {
        return $this->promoCode;
    }

    public function setPromoCode(?PromoCode $promoCode): self
    {
        $this->promoCode = $promoCode;
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

    public function getWheelSpinOption()
    {
        return $this->wheelSpinOption;
    }

    public function setWheelSpinOption(?WheelSpinOption $wheelSpinOption = null)
    {
        $this->wheelSpinOption = $wheelSpinOption;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getSpinPrize()
    {
        return $this->spinPrize;
    }

    public function setSpinPrize(?string $spinPrize = null)
    {
        $this->spinPrize = $spinPrize;

        return $this;
    }

    public function getTelegramChatId(): ?string
    {
        return $this->telegramChatId;
    }

    public function setTelegramChatId(?string $telegramChatId): self
    {
        $this->telegramChatId = $telegramChatId;

        return $this;
    }

    public function getTelegramMessageId(): ?int
    {
        return $this->telegramMessageId;
    }

    public function setTelegramMessageId(?int $telegramMessageId): self
    {
        $this->telegramMessageId = $telegramMessageId;

        return $this;
    }

    /**
     * Add orderHasItems.
     *
     * @return $this
     */
    public function addOrderHasItem(OrderHasItem $orderHasItems)
    {
        $orderHasItems->setOrder($this);
        $this->orderHasItems[] = $orderHasItems;

        return $this;
    }

    /**
     * Remove orderHasItems.
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeOrderHasItem(OrderHasItem $orderHasItems)
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
            self::STATUS_NEW => 'new',
            self::STATUS_COMPLETED => 'completed',
            self::STATUS_CANCEL => 'cancel',
        ];
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_ORDER_CART => 'cart',
            self::TYPE_ORDER_QUICK => 'quick',
        ];
    }

    public static function getNameStatus($status)
    {
        $statuses = [
            self::STATUS_NEW => 'order.fields.statuses.new',
            self::STATUS_COMPLETED => 'order.fields.statuses.completed',
            self::STATUS_CANCEL => 'order.fields.statuses.cancel',
        ];

        return $statuses[$status];
    }
}
