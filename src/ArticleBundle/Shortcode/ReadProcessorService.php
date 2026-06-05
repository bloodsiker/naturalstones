<?php

namespace ArticleBundle\Shortcode;

use ArticleBundle\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use ShortcodeBundle\Processor\ProcessorInterface;

class ReadProcessorService implements ProcessorInterface
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

        $article = $this->entityManager->getRepository(Article::class)->find((int) $data['id']);

        return array_merge($data, ['article' => $article]);
    }
}