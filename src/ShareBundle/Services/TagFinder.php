<?php

namespace ShareBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use ShareBundle\Entity\Tag;

class TagFinder
{
    public const LIMIT_RESULTS = 20;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @param list<int> $excludeTags
     * @param list<int> $bookFile
     *
     * @return array<int, array{id: int, name: string, count: int}>
     */
    public function findTagsInText(string $text, array $excludeTags = [], array $bookFile = []): array
    {
        return $this->findTagsInTextBySubstr($text, $excludeTags, $bookFile);
    }

    /**
     * @param list<int> $excludeTags
     * @param list<int> $bookFile
     *
     * @return array<int, array{id: int, name: string, count: int}>
     */
    private function findTagsInTextBySubstr(string $text, array $excludeTags, array $bookFile): array
    {
        $text = $this->prepareTextForSearch($text);
        $foundTags = [];
        $results = $this->em->getRepository(Tag::class)->baseTagQueryBuilder($excludeTags);

        foreach ($results as $result) {
            $needle = ' ' . mb_strtolower($result['name'], 'utf8') . ' ';
            $count = mb_substr_count($text, $needle, 'utf8');

            if ($count > 0 && !in_array($result['id'], $excludeTags, false)) {
                $foundTags[(int) $result['id']] = [
                    'id' => (int) $result['id'],
                    'name' => $result['name'],
                    'count' => $count,
                ];
            }
        }

        uasort($foundTags, fn (array $a, array $b) => $a['count'] <=> $b['count']);

        if (count($foundTags) > self::LIMIT_RESULTS) {
            $foundTags = array_slice($foundTags, 0, self::LIMIT_RESULTS, true);
        }

        return $foundTags;
    }

    public function prepareTextForSearch(string $text): string
    {
        $text = preg_replace('/<script([^>]*)>(.*?)<\/script>/sui', '', $text);
        $text = strip_tags($text);

        $words = array_map(static function (string $word): string {
            $word = trim($word);

            return $word && mb_strlen($word) >= 3 ? $word : '';
        }, explode(' ', $text));
        $text = implode(' ', $words);

        $text = mb_strtolower($text, 'utf8');
        $text = preg_replace('/&[a-z0-9]+;/i', '', $text);
        $text = str_replace([
            ' - ', '#', '+', '!', '?', '&', '«', '»', ',', '.', '(', ')', '{', '}',
            '\\', '"', '<', '>', '*', ';', ':', '%', "\n", "\r", "\t", '`', "'",
        ], ' ', $text);
        $text = preg_replace('/[\w]*[0-9]+[\w]*/', ' ', $text);

        while (false !== strpos($text, '  ')) {
            $text = str_replace('  ', ' ', $text);
        }

        return ' ' . $text . ' ';
    }
}