<?php

namespace MediaBundle\Shortcode;

use Doctrine\ORM\EntityManagerInterface;
use ShortcodeBundle\Processor\ProcessorInterface;

/**
 * Class ImageProcessorService
 */
class ImageProcessorService implements ProcessorInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * ImageProcessorService constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function process(array $data)
    {
        if (!(is_array($data) && array_key_exists('id', $data))) {
            return $data;
        }

        $image = $this->entityManager
            ->getRepository('MediaBundle:MediaImage')
            ->findOneBy(['id' => intval($data['id']), 'isActive' => true])
        ;

        return array_merge($data, ['image' => $image]);
    }
}
