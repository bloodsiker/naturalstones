<?php

namespace AppBundle\Twig;

use Doctrine\Common\Collections\Collection;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SortByFieldExtension extends AbstractExtension
{
    public function getName(): string
    {
        return 'sortbyfield';
    }

    /**
     * @return list<TwigFilter>
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('sortbyfield', $this->sortByFieldFilter(...)),
        ];
    }

    /**
     * Usage: {% for entry in master.entries|sortbyfield('orderNum', 'desc') %}
     *
     * @param Collection<int, mixed>|array<int, mixed> $content
     *
     * @return array<int, mixed>
     */
    public function sortByFieldFilter(
        Collection|array $content,
        ?string $sortBy = null,
        string $direction = 'asc',
    ): array {
        if ($content instanceof Collection) {
            $content = $content->toArray();
        }

        if (count($content) < 1) {
            return $content;
        }
        if (null === $sortBy) {
            throw new \InvalidArgumentException('No sort by parameter passed to the sortByField filter');
        }
        if (!self::isSortable(current($content), $sortBy)) {
            throw new \InvalidArgumentException(sprintf('Entries passed to the sortByField filter do not have the field "%s"', $sortBy));
        }

        $flip = 'desc' === mb_strtolower($direction) ? -1 : 1;
        usort($content, function ($a, $b) use ($sortBy, $flip) {
            return $flip * ($this->getSortValue($a, $sortBy) <=> $this->getSortValue($b, $sortBy));
        });

        return $content;
    }

    private function getSortValue(mixed $value, string $sortBy): mixed
    {
        if (is_array($value)) {
            return $value[$sortBy];
        }

        $getter = 'get' . ucfirst($sortBy);
        if (method_exists($value, $getter)) {
            return $value->$getter();
        }

        return $value->$sortBy;
    }

    private static function isSortable(mixed $item, string $field): bool
    {
        if (is_array($item)) {
            return array_key_exists($field, $item);
        }
        if (is_object($item)) {
            return isset($item->$field) || property_exists($item, $field);
        }

        return false;
    }
}