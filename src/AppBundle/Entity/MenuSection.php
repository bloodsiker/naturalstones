<?php

namespace AppBundle\Entity;

use AppBundle\Traits\TranslatableProxyTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

#[ORM\Table(name: 'app_menu_section')]
#[ORM\Entity]
class MenuSection implements TranslatableInterface
{
    use ORMBehaviors\Translatable\TranslatableTrait;
    use TranslatableProxyTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    #[ORM\Column(type: 'boolean', nullable: false)]
    protected $isActive;

    #[ORM\Column(name: 'order_num', type: 'integer', nullable: false, options: ['default' => 0])]
    protected $orderNum;

    #[ORM\OneToMany(targetEntity: \AppBundle\Entity\MenuItem::class, mappedBy: 'menuSection', cascade: ['all'], orphanRemoval: true)]
    #[ORM\OrderBy(['orderNum' => 'DESC'])]
    protected $items;

    public function __construct()
    {
        $this->isActive = true;
        $this->orderNum = 0;
        $this->items = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->title();
    }

    public function getId()
    {
        return $this->id;
    }

    public function title()
    {
        return $this->translate()->getTitle();
    }

    public function setIsActive(bool $isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function setOrderNum($orderNum)
    {
        $this->orderNum = $orderNum;

        return $this;
    }

    public function getOrderNum()
    {
        return $this->orderNum;
    }

    public function addItem(MenuItem $item)
    {
        if (!$this->items->contains($item)) {
            $item->setMenuSection($this);
            $this->items->add($item);
        }

        return $this;
    }

    public function removeItem(MenuItem $item)
    {
        $this->items->removeElement($item);
    }

    public function getItems(): Collection
    {
        return $this->items;
    }
}
