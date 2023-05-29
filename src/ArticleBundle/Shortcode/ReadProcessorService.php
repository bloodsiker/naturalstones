<?php

namespace ArticleBundle\Shortcode;

use Doctrine\ORM\EntityManagerInterface;
use ShortcodeBundle\Processor\ProcessorInterface;

/**
 * Class ReadProcessorService
 */
class ReadProcessorService implements ProcessorInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * ReadProcessorService constructor.
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

        $articleId = intval($data['id']);

        $article = $this->entityManager->getRepository('ArticleBundle:Article')->find($articleId);

        return array_merge($data, ['article' => $article]);
    }
}
