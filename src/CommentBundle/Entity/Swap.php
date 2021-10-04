<?php

namespace CommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Comment
 *
 * @ORM\Entity()
 * @ORM\Table(name="books_swap")
 * @ORM\HasLifecycleCallbacks
 */
class Swap
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
     * @ORM\Column(type="text", nullable=false)
     */
    protected $content;

    /**
     * @var \UserBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $user;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $userName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $userEmail;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $isActive;

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
        $this->isActive = true;
        $this->createdAt = new \DateTime('now');
    }

    /**
     * "String" representation of class
     *
     * @return string
     */
    public function __toString()
    {
        return 'Комментарии';
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
     * Set content
     *
     * @param string $content
     *
     * @return Swap
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     *
     * @return Swap
     */
    public function setUser(\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get userName
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Set userName
     *
     * @param string $userName
     *
     * @return $this
     */
    public function setUserName(string $userName)
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * Get userEmail
     *
     * @return string
     */
    public function getUserEmail()
    {
        return $this->userEmail;
    }

    /**
     * Set userEmail
     *
     * @param string $userEmail
     *
     * @return $this
     */
    public function setUserEmail(string $userEmail = null)
    {
        $this->userEmail = $userEmail;

        return $this;
    }

    /**
     * Set isActive
     *
     * @param bool $isActive
     *
     * @return Swap
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Swap
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
}
