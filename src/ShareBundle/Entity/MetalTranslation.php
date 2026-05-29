<?php

namespace ShareBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Class MetalTranslation
 *
 * @ORM\Entity()
 * @ORM\Table(name="share_metals_translation")
 * @ORM\HasLifecycleCallbacks
 */
class MetalTranslation implements TranslationInterface
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
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $name;

    /**
     * Set name
     *
     * @param string $name
     *
     * @return MetalTranslation
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
