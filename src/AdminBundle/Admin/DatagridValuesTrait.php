<?php

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Datagrid\DatagridInterface;

trait DatagridValuesTrait
{
    protected function configureDefaultSortValues(array &$sortValues): void
    {
        parent::configureDefaultSortValues($sortValues);

        if (!isset($this->datagridValues) || !is_array($this->datagridValues)) {
            return;
        }

        foreach ([DatagridInterface::PAGE, DatagridInterface::PER_PAGE, DatagridInterface::SORT_BY, DatagridInterface::SORT_ORDER] as $key) {
            if (array_key_exists($key, $this->datagridValues)) {
                $sortValues[$key] = $this->datagridValues[$key];
            }
        }
    }

    protected function configureDefaultFilterValues(array &$filterValues): void
    {
        parent::configureDefaultFilterValues($filterValues);

        if (!isset($this->datagridValues) || !is_array($this->datagridValues)) {
            return;
        }

        foreach ($this->datagridValues as $key => $value) {
            if (!is_string($key) || str_starts_with($key, '_')) {
                continue;
            }

            $filterValues[$key] = $value;
        }
    }
}
