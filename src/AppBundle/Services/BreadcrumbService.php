<?php

namespace AppBundle\Services;

class BreadcrumbService
{
    /** @var array<int, array<string, mixed>> */
    private array $breadcrumb = [];

    /**
     * @param array<string, mixed> $array
     */
    public function addBreadcrumb(array $array): self
    {
        $this->breadcrumb[] = $array;

        return $this;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getBreadcrumb(): array
    {
        return $this->breadcrumb;
    }
}