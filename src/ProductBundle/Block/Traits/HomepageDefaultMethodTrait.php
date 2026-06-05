<?php

namespace ProductBundle\Block\Traits;

use AppBundle\Services\SaveStateValue;
use Doctrine\Bundle\DoctrineBundle\Registry;
use ProductBundle\Entity\Category;
use ShareBundle\Entity\Colour;
use ShareBundle\Entity\Stone;
use ShareBundle\Entity\Tag;

trait HomepageDefaultMethodTrait
{
    private Registry $doctrine;
    private SaveStateValue $stateValue;

    /** @var array<string, object>|null */
    protected ?array $admins = null;

    /** @var array<string, class-string> */
    protected array $entities = [
        'category' => Category::class,
        'tag' => Tag::class,
        'stone' => Stone::class,
        'colour' => Colour::class,
    ];

    public function setDoctrine(Registry $doctrine): void
    {
        $this->doctrine = $doctrine;
    }

    public function setStateValue(SaveStateValue $stateValue): void
    {
        $this->stateValue = $stateValue;
    }

    /**
     * @param array<string, object>|null $admins
     */
    public function setAdmins(?array $admins): void
    {
        $this->admins = $admins;
    }
}