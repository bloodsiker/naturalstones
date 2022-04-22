<?php

namespace ShareBundle\Shortcode;

use Doctrine\ORM\EntityManagerInterface;
use ShortcodeBundle\Processor\ProcessorInterface;

/**
 * Class TextProcessorService
 */
class TextProcessorService implements ProcessorInterface
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

        $text = $this->entityManager
            ->getRepository('ShareBundle:Text')
            ->findOneBy(['id' => intval($data['id']), 'isActive' => true])
        ;

        return array_merge($data, ['text' => $text]);
    }
}
