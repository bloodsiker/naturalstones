<?php

namespace WheelSpinBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Class WheelSpinOptionTranslation
 *
 * @ORM\Entity()
 * @ORM\Table(name="wheel_spin_option_translation")
 * @ORM\HasLifecycleCallbacks
 */
class WheelSpinOptionTranslation implements TranslationInterface
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
    protected $title;

    /**
     * Set name
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
