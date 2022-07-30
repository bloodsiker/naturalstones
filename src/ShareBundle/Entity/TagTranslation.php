<?php

namespace ShareBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Class TagTranslation
 *
 * @ORM\Entity()
 * @ORM\Table(name="share_tags_translation")
 * @ORM\HasLifecycleCallbacks
 */
class TagTranslation
{
    use ORMBehaviors\Translatable\Translation;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $name;

    /**
     * Set name
     *
     * @param string $name
     *
     * @return TagTranslation
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
