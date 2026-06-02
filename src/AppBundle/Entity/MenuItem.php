<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ProductBundle\Entity\Category;

/**
 * @ORM\Entity
 * @ORM\Table(name="app_menu_item")
 */
class MenuItem
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MenuSection", inversedBy="items")
     * @ORM\JoinColumn(name="menu_section_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $menuSection;

    /**
     * @ORM\ManyToOne(targetEntity="ProductBundle\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $category;

    /**
     * @ORM\Column(name="order_num", type="integer", nullable=false, options={"default": 0})
     */
    protected $orderNum;

    public function __construct()
    {
        $this->orderNum = 0;
    }

    public function __toString()
    {
        return (string) $this->category;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getMenuSection()
    {
        return $this->menuSection;
    }

    public function setMenuSection(MenuSection $menuSection)
    {
        $this->menuSection = $menuSection;

        return $this;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory(Category $category)
    {
        $this->category = $category;

        return $this;
    }

    public function getOrderNum()
    {
        return $this->orderNum;
    }

    public function setOrderNum($orderNum)
    {
        $this->orderNum = $orderNum;

        return $this;
    }
}
