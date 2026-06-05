<?php

namespace AppBundle\Template;

use Pagerfanta\PagerfantaInterface;
use Pagerfanta\View\Template\TemplateInterface;
use Pagerfanta\View\ViewInterface;

class DefaultView implements ViewInterface
{
    private ?PagerfantaInterface $pagerfanta = null;
    private int $proximity = 3;
    private int $currentPage = 1;
    private int $nbPages = 1;
    private int $startPage = 1;
    private int $endPage = 1;

    /** @var list<array{0: string, 1: string}> */
    private array $extraPages = [];

    public function __construct(
        private readonly ?TemplateInterface $template = null,
    ) {
    }

    public function getName(): string
    {
        return 'default';
    }

    /**
     * @param array<string, mixed> $options
     */
    public function render(PagerfantaInterface $pagerfanta, callable $routeGenerator, array $options = []): string
    {
        $this->initializePagerfanta($pagerfanta);
        $this->initializeOptions($options);
        $this->template->setRouteGenerator($routeGenerator);
        $this->template->setOptions($options);

        return $this->generate();
    }

    /**
     * @param list<array{0: string, 1: string}> $extraPages
     */
    public function setExtraPages(array $extraPages): self
    {
        $this->extraPages = $extraPages;

        return $this;
    }

    /**
     * @param array{0: string, 1: string} $extraPage
     */
    public function addExtraPage(array $extraPage): self
    {
        $this->extraPages[] = $extraPage;

        return $this;
    }

    protected function getDefaultProximity(): int
    {
        return 3;
    }

    private function initializePagerfanta(PagerfantaInterface $pagerfanta): void
    {
        $this->pagerfanta = $pagerfanta;
        $this->currentPage = $pagerfanta->getCurrentPage();
        $this->nbPages = $pagerfanta->getNbPages();
    }

    /**
     * @param array<string, mixed> $options
     */
    private function initializeOptions(array $options): void
    {
        $this->proximity = isset($options['proximity'])
            ? (int) $options['proximity']
            : $this->getDefaultProximity();
    }

    private function generate(): string
    {
        $pages = $this->generatePages();

        return str_replace(
            ['%prev%', '%next%'],
            [$this->previous() ?? '', $this->next() ?? ''],
            str_replace('%pages%', $pages . $this->generateExtraPages(), $this->template->container())
        );
    }

    private function generatePages(): string
    {
        $this->calculateStartAndEndPage();

        return ($this->first() ?? '')
            . ($this->secondIfStartIs3() ?? '')
            . ($this->dotsIfStartIsOver3() ?? '')
            . $this->pages()
            . ($this->dotsIfEndIsUnder3ToLast() ?? '')
            . ($this->secondToLastIfEndIs3ToLast() ?? '')
            . ($this->last() ?? '');
    }

    private function generateExtraPages(): string
    {
        $pages = '';
        foreach ($this->extraPages as [$href, $text]) {
            $pages .= $this->template->extraPageWithText($href, $text);
        }

        return $pages;
    }

    private function calculateStartAndEndPage(): void
    {
        $startPage = $this->currentPage - $this->proximity;
        $endPage = $this->currentPage + $this->proximity;

        if ($startPage < 1) {
            $endPage = min($endPage + (1 - $startPage), $this->nbPages);
            $startPage = 1;
        }
        if ($endPage > $this->nbPages) {
            $startPage = max($startPage - ($endPage - $this->nbPages), 1);
            $endPage = $this->nbPages;
        }

        $this->startPage = $startPage;
        $this->endPage = $endPage;
    }

    private function first(): ?string
    {
        return $this->startPage > 1 ? $this->template->first() : null;
    }

    private function previous(): ?string
    {
        return $this->pagerfanta->hasPreviousPage()
            ? $this->template->previousEnabled($this->pagerfanta->getPreviousPage())
            : null;
    }

    private function secondIfStartIs3(): ?string
    {
        return 3 === $this->startPage ? $this->template->page(2) : null;
    }

    private function dotsIfStartIsOver3(): ?string
    {
        return $this->startPage > 3 ? $this->template->separator() : null;
    }

    private function pages(): string
    {
        $pages = '';
        foreach (range($this->startPage, $this->endPage) as $page) {
            $pages .= $this->page($page);
        }

        return $pages;
    }

    private function dotsIfEndIsUnder3ToLast(): ?string
    {
        return $this->endPage < $this->toLast(3) ? $this->template->separator() : null;
    }

    private function page(int $page): string
    {
        return $page === $this->currentPage
            ? $this->template->current($page)
            : $this->template->page($page);
    }

    private function secondToLastIfEndIs3ToLast(): ?string
    {
        return $this->endPage === $this->toLast(3)
            ? $this->template->page($this->toLast(2))
            : null;
    }

    private function toLast(int $n): int
    {
        return $this->pagerfanta->getNbPages() - ($n - 1);
    }

    private function last(): ?string
    {
        return $this->pagerfanta->getNbPages() > $this->endPage
            ? $this->template->last($this->pagerfanta->getNbPages())
            : null;
    }

    private function next(): ?string
    {
        return $this->pagerfanta->hasNextPage()
            ? $this->template->nextEnabled($this->pagerfanta->getNextPage())
            : null;
    }
}