<?php

namespace AppBundle\Template;

use Pagerfanta\Pagerfanta;
use Pagerfanta\PagerfantaInterface;
use Pagerfanta\View\Template\TemplateInterface;
use Pagerfanta\View\ViewInterface;
use Sonata\AdminBundle\Route\RouteGeneratorInterface;

/**
 * Class DefaultView
 */
class DefaultView implements ViewInterface
{
    private $template;

    private $pagerfanta;
    private $proximity;

    private $currentPage;
    private $nbPages;

    private $startPage;
    private $endPage;
    private $extraPages = [];

    /**
     * DefaultView constructor.
     * @param TemplateInterface|null $template
     */
    public function __construct(TemplateInterface $template = null)
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'default';
    }

    /**
     * @param PagerfantaInterface $pagerfanta
     * @param callable            $routeGenerator
     * @param array               $options
     *
     * @return mixed
     */
    public function render(PagerfantaInterface $pagerfanta, $routeGenerator, array $options = array())
    {
        $this->initializePagerfanta($pagerfanta);
        $this->initializeOptions($options);

        $this->configureTemplate($routeGenerator, $options);

        return $this->generate();
    }

    /**
     * @param array $extraPages
     *
     * @return DefaultView
     */
    public function setExtraPages($extraPages)
    {
        $this->extraPages = $extraPages;

        return $this;
    }


    /**
     * @param array $extraPage
     *
     * @return DefaultView
     */
    public function addExtraPage($extraPage)
    {
        $this->extraPages[] = $extraPage;

        return $this;
    }

    /**
     * @return int
     */
    protected function getDefaultProximity()
    {
        return 3;
    }

    /**
     * @param PagerfantaInterface $pagerfanta
     */
    private function initializePagerfanta(PagerfantaInterface $pagerfanta)
    {
        $this->pagerfanta = $pagerfanta;

        $this->currentPage = $pagerfanta->getCurrentPage();
        $this->nbPages = $pagerfanta->getNbPages();
    }

    /**
     * @param array $options
     */
    private function initializeOptions($options)
    {
        $this->proximity = isset($options['proximity']) ?
            (int) $options['proximity'] :
            $this->getDefaultProximity();
    }

    /**
     * @param RouteGeneratorInterface $routeGenerator
     * @param array                   $options
     */
    private function configureTemplate($routeGenerator, $options)
    {
        $this->template->setRouteGenerator($routeGenerator);
        $this->template->setOptions($options);
    }

    /**
     * @return mixed
     */
    private function generate()
    {
        $pages = $this->generatePages();

        return $this->generateContainer($pages);
    }

    /**
     * @param string $pages
     *
     * @return mixed
     */
    private function generateContainer($pages)
    {
        return str_replace(
            ['%prev%', '%next%'],
            [$this->previous(), $this->next()],
            str_replace('%pages%', $pages.$this->generateExtraPages(), $this->template->container())
        );
    }

    /**
     * @return string
     */
    private function generatePages()
    {
        $this->calculateStartAndEndPage();

        return $this->first().
            $this->secondIfStartIs3().
            $this->dotsIfStartIsOver3().
            $this->pages().
            $this->dotsIfEndIsUnder3ToLast().
            $this->secondToLastIfEndIs3ToLast().
            $this->last();
    }


    /**
     * @return string
     */
    private function generateExtraPages()
    {
        $pages = '';
        foreach ($this->extraPages as $page) {
            list($href, $text) = $page;
            $pages .= $this->template->extraPageWithText($href, $text);
        }

        return $pages;
    }

    /**
     * @return void
     */
    private function calculateStartAndEndPage()
    {
        $startPage = $this->currentPage - $this->proximity;
        $endPage = $this->currentPage + $this->proximity;

        if ($this->startPageUnderflow($startPage)) {
            $endPage = $this->calculateEndPageForStartPageUnderflow($startPage, $endPage);
            $startPage = 1;
        }
        if ($this->endPageOverflow($endPage)) {
            $startPage = $this->calculateStartPageForEndPageOverflow($startPage, $endPage);
            $endPage = $this->nbPages;
        }

        $this->startPage = $startPage;
        $this->endPage = $endPage;
    }

    /**
     * @param int $startPage
     *
     * @return bool
     */
    private function startPageUnderflow($startPage)
    {
        return $startPage < 1;
    }

    /**
     * @param int $endPage
     *
     * @return bool
     */
    private function endPageOverflow($endPage)
    {
        return $endPage > $this->nbPages;
    }

    /**
     * @param int $startPage
     * @param int $endPage
     *
     * @return mixed
     */
    private function calculateEndPageForStartPageUnderflow($startPage, $endPage)
    {
        return min($endPage + (1 - $startPage), $this->nbPages);
    }

    /**
     * @param int $startPage
     * @param int $endPage
     *
     * @return mixed
     */
    private function calculateStartPageForEndPageOverflow($startPage, $endPage)
    {
        return max($startPage - ($endPage - $this->nbPages), 1);
    }

    /**
     * @return string
     */
    private function first()
    {
        if ($this->startPage > 1) {
            return $this->template->first();
        }
    }

    /**
     * @return string
     */
    private function previous()
    {
        if ($this->pagerfanta->hasPreviousPage()) {
            return $this->template->previousEnabled($this->pagerfanta->getPreviousPage());
        }

        return null;
    }

    /**
     * @return string
     */
    private function secondIfStartIs3()
    {
        if ($this->startPage === 3) {
            return $this->template->page(2);
        }
    }

    /**
     *  @return string
     */
    private function dotsIfStartIsOver3()
    {
        if ($this->startPage > 3) {
            return $this->template->separator();
        }
    }

    /**
     * @return string
     */
    private function pages()
    {
        $pages = '';

        foreach (range($this->startPage, $this->endPage) as $page) {
            $pages .= $this->page($page);
        }

        return $pages;
    }

    /**
     * @return string
     */
    private function dotsIfEndIsUnder3ToLast()
    {
        if ($this->endPage < $this->toLast(3)) {
            return $this->template->separator();
        }
    }

        /**
     * @param int $page
     *
     * @return string
     */
    private function page($page)
    {
        if ($page === $this->currentPage) {
            return $this->template->current($page);
        }

        return $this->template->page($page);
    }

    /**
     * @return string
     */
    private function secondToLastIfEndIs3ToLast()
    {
        if ($this->endPage === $this->toLast(3)) {
            return $this->template->page($this->toLast(2));
        }
    }

    /**
     * @param int $n
     *
     * @return mixed
     */
    private function toLast($n)
    {
        return $this->pagerfanta->getNbPages() - ($n - 1);
    }

    /**
     * @return string
     */
    private function last()
    {
        if ($this->pagerfanta->getNbPages() > $this->endPage) {
            return $this->template->last($this->pagerfanta->getNbPages());
        }
    }

    /**
     * @return string
     */
    private function next()
    {
        if ($this->pagerfanta->hasNextPage()) {
            return $this->template->nextEnabled($this->pagerfanta->getNextPage());
        }

        return null;
//        return $this->template->nextDisabled();
    }
}
