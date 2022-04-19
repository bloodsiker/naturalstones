<?php

namespace ProductBundle\Block\Traits;

use AppBundle\Services\SaveStateValue;
use Doctrine\Bundle\DoctrineBundle\Registry;
use ProductBundle\Entity\Category;
use ShareBundle\Entity\Colour;
use ShareBundle\Entity\Stone;
use ShareBundle\Entity\Tag;

/**
 * Trait HomepageDefaultMethodTrait
 */
trait HomepageDefaultMethodTrait
{
    /**
     * @var Registry $doctrine
     */
    private $doctrine;

    /**
     * @var SaveStateValue
     */
    private $stateValue;

    /**
     * @var ArticleAdmin|null
     */
    protected $admins;

    /**
     * @var array
     */
    protected $entities = [
        'category'           => Category::class,
        'tag'                => Tag::class,
        'stone'              => Stone::class,
        'colour'             => Colour::class,
    ];

    /**
     * @param Registry $doctrine
     */
    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param SaveStateValue $stateValue
     */
    public function setStateValue(SaveStateValue $stateValue)
    {
        $this->stateValue = $stateValue;
    }

    /**
     * @param mixed $admins
     */
    public function setAdmins($admins)
    {
        $this->admins = $admins;
    }

//    /**
//     * @return array $choice
//     */
//    protected function getTypeChoice()
//    {
//        $list = $this->doctrine->getRepository(ArticleType::class)
//            ->findBy(['isActive' => true, 'isHidden' => false ]);
//
//        $choice = [];
//        foreach ($list as $value) {
//            $choice[$value->translate()->getTitle()] = $value->getId();
//        }
//
//        return $choice;
//    }
}
