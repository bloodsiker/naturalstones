<?php

namespace ShareBundle\Shortcode;

use Doctrine\ORM\EntityManagerInterface;
use ShareBundle\Entity\Text;
use ShortcodeBundle\Processor\ProcessorInterface;

class TextProcessorService implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public function process(array $data): array
    {
        if (!array_key_exists('id', $data)) {
            return $data;
        }

        $text = $this->entityManager
            ->getRepository(Text::class)
            ->findOneBy(['id' => (int) $data['id'], 'isActive' => true]);

        return array_merge($data, ['text' => $text]);
    }
}