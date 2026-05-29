<?php

namespace ShareBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use ShareBundle\Entity\Tag;

class TagFinder
{
    const LIMIT_RESULTS = 20;

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function findTagsInText($text, $excludeTags = [], $bookFile = []): array
    {
        return $this->findTagsInTextBySubstr($text, $excludeTags, $bookFile);
    }

    private function findTagsInTextBySubstr($text, $excludeTags, $bookFile): array
    {
        $text = $this->prepareTextForSearch($text);

        $foundTags = [];
        $results = $this->em->getRepository(Tag::class)->baseTagQueryBuilder($excludeTags);

        foreach ($results as $result) {
            $count = mb_substr_count($text, " " . mb_strtolower($result['name'], "utf8") . " ", "utf8");
            if ($count > 0 && !in_array($result['id'], $excludeTags)) {
                $foundTags[$result['id']] = [
                    'count' => intval($count),
                    'name'  => $result['name'],
                ];
            }
        }

        uasort($foundTags, fn($a, $b) => $a['count'] <=> $b['count']);

        if (count($foundTags) > self::LIMIT_RESULTS) {
            $foundTags = array_slice($foundTags, 0, self::LIMIT_RESULTS, true);
        }

        foreach ($foundTags as $id => $tag) {
            $foundTags[$id]['id']    = $id;
            $foundTags[$id]['name']  = $tag['name'];
            $foundTags[$id]['count'] = $tag['count'];
        }

        return $foundTags;
    }

    public function prepareTextForSearch($text): string
    {
        $text = preg_replace('/<script([^>]*)>(.*?)<\/script>/sui', '', $text);
        $text = strip_tags($text);

        $words = explode(" ", $text);
        $words = array_map(function ($word) {
            $word = trim($word);
            return $word && mb_strlen($word) >= 3 ? $word : '';
        }, $words);
        $text = implode(" ", $words);

        $text = mb_strtolower($text, "utf8");
        $text = preg_replace('/&[a-z0-9]+;/i', '', $text);
        $text = str_replace([' - ', '#', '+', '!', '?', '&', '«', '»', ',', '.', '(', ')', '{', '}', '\\', '"', '<', '>', '*', ';', ':', '%', "\n", "\r", "\t", "`", "'"], ' ', $text);
        $text = preg_replace('/[\w]*[0-9]+[\w]*/', ' ', $text);

        while (false !== strpos($text, '  ')) {
            $text = str_replace('  ', ' ', $text);
        }

        return " " . $text . " ";
    }
}