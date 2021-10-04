<?php

namespace ShareBundle\Services;

use ShareBundle\Entity\Tag;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class TagFinder
 */
class TagFinder
{
    use ContainerAwareTrait;

    const LIMIT_RESULTS = 20;

    /**
     * @param string $text
     * @param array  $excludeTags
     * @param array  $bookFile
     *
     * @return array
     *
     * @throws \Exception
     */
    public function findTagsInText($text, $excludeTags = [], $bookFile = [])
    {
        $foundTags = $this->findTagsInTextBySubstr($text, $excludeTags, $bookFile);

        return $foundTags;
    }

    /**
     * @param string $text
     * @param array  $excludeTags
     * @param array  $bookFile
     *
     * @return array
     */
    private function findTagsInTextBySubstr($text, $excludeTags, $bookFile)
    {
        $text = $this->prepareTextForSearch($text);

        $foundTags = [];

        $repository = $this->container->get('doctrine')->getManager()->getRepository(Tag::class);
        $results = $repository->baseTagQueryBuilder($excludeTags);

        foreach ($results as $result) {
            $count = mb_substr_count($text, " ".mb_strtolower($result['name'], "utf8")." ", "utf8");
            if ($count > 0 && !in_array($result['id'], $excludeTags)) {
                $foundTags[$result['id']] = [
                    'count' => intval($count),
                    'name' => $result['name'],
                ];
            }
        }

        uasort($foundTags, function ($a, $b) {
            return $a['count'] <=> $b['count'];
        });

        if (count($foundTags) > self::LIMIT_RESULTS) {
            $foundTags = array_slice($foundTags, 0, self::LIMIT_RESULTS, true);
        }

        foreach ($foundTags as $id => $tag) {
            $foundTags[$id]['id'] = $id;
            $foundTags[$id]['name'] = $tag['name'];
            $foundTags[$id]['count'] = $tag['count'];
        }

        return $foundTags;
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function prepareTextForSearch($text)
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
        $text = " ".$text." ";

        return $text;
    }
}
