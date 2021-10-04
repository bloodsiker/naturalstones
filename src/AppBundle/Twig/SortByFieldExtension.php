<?php

namespace AppBundle\Twig;

use Doctrine\Common\Collections\Collection;

/**
 * Class SortByFieldExtension
 */
class SortByFieldExtension extends \Twig_Extension
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'sortbyfield';
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('sortbyfield', [$this, 'sortByFieldFilter']),
        ];
    }

    /**
     * Usage: {% for entry in master.entries|sortbyfield('orderNum', 'desc') %}
     *
     * @param Collection  $content
     * @param null|string $sortBy
     * @param string      $direction
     *
     * @return array
     *
     * @throws \Exception
     */
    public function sortByFieldFilter($content, $sortBy = null, $direction = 'asc')
    {
        if (is_a($content, Collection::class)) {
            $content = $content->toArray();
        }

        if (!is_array($content)) {
            throw new \InvalidArgumentException('Variable passed to the sortByField filter is not an array');
        } elseif (count($content) < 1) {
            return $content;
        } elseif (null === $sortBy) {
            throw new \Exception('No sort by parameter passed to the sortByField filter');
        } elseif (!self::isSortable(current($content), $sortBy)) {
            throw new \Exception('Entries passed to the sortByField filter do not have the field "' . $sortBy . '"');
        } else {
            usort($content, function ($a, $b) use ($sortBy, $direction) {
                $flip = (mb_strtolower($direction) === 'desc') ? -1 : 1;

                $aSortValue = $this->getSortValue($a, $sortBy);
                $bSortValue = $this->getSortValue($b, $sortBy);

                if ($aSortValue === $bSortValue) {
                    $result = 0;
                } elseif ($aSortValue > $bSortValue) {
                    $result = (1 * $flip);
                } else {
                    $result = (-1 * $flip);
                }

                return $result;
            });
        }

        return $content;
    }

    /**
     * @param mixed  $value
     * @param string $sortBy
     *
     * @return mixed
     */
    private function getSortValue($value, $sortBy)
    {
        if (is_array($value)) {
            $sortValue = $value[$sortBy];
        } elseif (method_exists($value, 'get'.ucfirst($sortBy))) {
            $sortValue = $value->{'get'.ucfirst($sortBy)}();
        } else {
            $sortValue = $value->$sortBy;
        }

        return $sortValue;
    }

    /**
     * Validate the passed $item to check if it can be sorted
     *
     * @param mixed  $item Collection item to be sorted
     * @param string $field
     *
     * @return bool If collection item can be sorted
     */
    private static function isSortable($item, $field)
    {
        if (is_array($item)) {
            return array_key_exists($field, $item);
        } elseif (is_object($item)) {
            return isset($item->$field) || property_exists($item, $field);
        } else {
            return false;
        }
    }
}
