<?php

namespace ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class BookInfoDownload
 *
 * @ORM\Entity()
 * @ORM\Table(name="product_search_history")
 * @ORM\HasLifecycleCallbacks
 */
class ProductSearchHistory
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
    protected $search;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    protected $ip;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
    }

    /**
     * "String" representation of class
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->search;
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
     * Set createdAt.
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
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set ip
     *
     * @param string|null $ip
     *
     * @return $this
     */
    public function setIp(string $ip = null)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * get search
     *
     * @return string
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * set search
     *
     * @param  string|null  $search
     *
     * @return $this
     */
    public function setSearch(string $search = null)
    {
        $this->search = $search;

        return $this;
    }
}
