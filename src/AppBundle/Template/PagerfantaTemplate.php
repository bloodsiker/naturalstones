<?php

namespace AppBundle\Template;

use Pagerfanta\View\Template\DefaultTemplate;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PagerfantaTemplate
 */
class PagerfantaTemplate extends DefaultTemplate
{
    private ?TranslatorInterface $translator = null;

    protected function getDefaultOptions(): array
    {
        return array_merge(parent::getDefaultOptions(), [
            'prev_message' => '<li><a href="%href%"><i class="arrow prev"></i></a></li>',
            'next_message' => '<li class="next"><a href="%href%"><i class="arrow next"></i></a></li>',
            'css_disabled_class' => '',
            'css_dots_class' => 'nav_ext',
            'css_active_class' => 'active',
            'css_container_class' => 'pagination',
            'dots_message' => '...',
            'container_template' => '<ul class="%s">%%prev%% %%pages%% %%next%%</ul>',
            'page_template' => '<li><a class="%class%" href="%href%"%rel%>%text%</a></li>',
            'span_template' => '<li class="%class%"><span>%text%</span></li>',
            'rel_previous' => 'prev',
            'rel_next' => 'next',
        ]);
    }

    public function pageWithText(int $page, string $text, ?string $rel = null): string
    {
        return $this->renderPageTemplate($this->generateRoute($page), $text, $rel);
    }

    public function extraPageWithText(string $href, string $text, ?string $rel = null): string
    {
        return $this->renderPageTemplate($href, $text, $rel);
    }

    public function current(int $page): string
    {
        return $this->generateSpan($this->option('css_active_class'), (string) $page);
    }

    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    public function previousDisabled(): string
    {
        return $this->option('prev_message');
    }

    public function previousEnabled(int $page): string
    {
        return str_replace('%href%', $this->generateRoute($page), $this->previousDisabled());
    }

    public function nextDisabled(): string
    {
        return $this->option('next_message');
    }

    public function nextEnabled(int $page): string
    {
        return str_replace('%href%', $this->generateRoute($page), $this->nextDisabled());
    }

    public function init(string $domain): void
    {
        $defaultOptions = $this->getDefaultOptions();

        $this->setOptions([
            'prev_message' => str_replace(
                '%prev_text%',
                $this->translator->trans('app.pager.previous', [], $domain),
                $defaultOptions['prev_message']
            ),
            'next_message' => str_replace(
                '%next_text%',
                $this->translator->trans('app.pager.next', [], $domain),
                $defaultOptions['next_message']
            ),
        ]);
    }

    private function renderPageTemplate(string $href, string $text, ?string $rel): string
    {
        $replace = ['', $href, $text, $rel ? ' rel="' . $rel . '"' : ''];

        return str_replace(['%class%', '%href%', '%text%', '%rel%'], $replace, $this->option('page_template'));
    }

    private function generateSpan(string $class, string $text): string
    {
        return str_replace(['%class%', '%text%'], [$class, $text], $this->option('span_template'));
    }
}
