<?php

namespace MainImageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Class MainImageTranslation
 *
 * @ORM\Entity()
 * @ORM\Table(name="main_image_translation")
 * @ORM\HasLifecycleCallbacks
 */
class MainImageTranslation
{
    use ORMBehaviors\Translatable\Translation;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * Set title
     *
     * @param string $title
     *
     * @return MainImageTranslation
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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
     * Set description
     *
     * @param string $description
     *
     * @return MainImageTranslation
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }
}
