<?php

namespace ArticleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Class CategoryTranslation
 *
 * @ORM\Entity()
 * @ORM\Table(name="article_category_translation")
 * @ORM\HasLifecycleCallbacks
 */
class CategoryTranslation
{
    use ORMBehaviors\Translatable\Translation;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * Set name
     *
     * @param string $name
     *
     * @return CategoryTranslation
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
