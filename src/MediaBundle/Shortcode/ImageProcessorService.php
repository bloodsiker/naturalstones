<?php

namespace MediaBundle\Shortcode;

use Doctrine\ORM\EntityManagerInterface;
use MediaBundle\Entity\MediaImage;
use ShortcodeBundle\Processor\ProcessorInterface;

class ImageProcessorService implements ProcessorInterface
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

        $image = $this->entityManager
            ->getRepository(MediaImage::class)
            ->findOneBy(['id' => (int) $data['id'], 'isActive' => true]);

        return array_merge($data, ['image' => $image]);
    }
}