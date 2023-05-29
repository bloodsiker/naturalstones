<?php

namespace ArticleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Class ArticleTranslation
 *
 * @ORM\Entity()
 * @ORM\Table(name="article_article_translation")
 * @ORM\HasLifecycleCallbacks
 */
class ArticleTranslation
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
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $shortDescription;

    /**
     * Set title
     *
     * @param string $title
     *
     * @return ArticleTranslation
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
     * @return ArticleTranslation
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * @param  string  $shortDescription
     *
     * @return $this
     */
    public function setShortDescription($shortDescription = null)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }
}
