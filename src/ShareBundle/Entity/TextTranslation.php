<?php

namespace ShareBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Class TextTranslation
 */
#[ORM\Table(name: 'share_text_translation')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class TextTranslation implements TranslationInterface
{
    use ORMBehaviors\Translatable\TranslationTrait;

    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected $name;

    /**
     * @var string
     */
    #[ORM\Column(type: 'text', nullable: true)]
    protected $description;

    /**
     * Set name
     *
     * @param string $name
     *
     * @return TextTranslation
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
     * @return TextTranslation
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }
}
