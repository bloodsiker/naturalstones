<?php

namespace ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Class ProductTranslation
 *
 * @ORM\Entity()
 * @ORM\Table(name="product_product_translation")
 * @ORM\HasLifecycleCallbacks
 */
class ProductTranslation implements TranslationInterface
{
    use ORMBehaviors\Translatable\TranslationTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * Set name
     *
     * @param string $name
     *
     * @return ProductTranslation
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
     * @return ProductTranslation
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }
}
