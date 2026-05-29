<?php

namespace AppBundle\Template;

use Pagerfanta\View\Template\DefaultTemplate;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PagerfantaTemplate
 */
class PagerfantaTemplate extends DefaultTemplate
{
    protected function getDefaultOptions(): array
    {
        return array_merge(parent::getDefaultOptions(), [
            'prev_message'       => '<li><a href="%href%"><i class="arrow prev"></i></a></li>',
            'next_message'       => '<li class="next"><a href="%href%"><i class="arrow next"></i></a></li>',
            'css_disabled_class' => '',
            'css_dots_class'     => 'nav_ext',
            'css_active_class'   => 'active',
            'css_container_class' => 'pagination',
            'dots_message'       => '...',
            'container_template' => '<ul class="%s">%%prev%% %%pages%% %%next%%</ul>',
            'page_template'      => '<li><a class="%class%" href="%href%"%rel%>%text%</a></li>',
            'span_template'      => '<li class="%class%"><span>%text%</span></li>',
            'rel_previous'       => 'prev',
            'rel_next'           => 'next',
        ]);
    }

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param int         $page
     * @param string      $text
     * @param null|string $rel
     *
     * @return mixed|string
     */
    public function pageWithText(int $page, string $text, ?string $rel = null) :string
    {
        $href = $this->generateRoute($page);
        $replace = $rel ? ['', $href, $text, ' rel="'.$rel.'"'] : ['', $href, $text, ''];

        return str_replace(['%class%', '%href%', '%text%', '%rel%'], $replace, $this->option('page_template'));
    }

    public function extraPageWithText($href, $text, $rel = null): string
    {
        $replace = $rel ? ['', $href, $text, ' rel="'.$rel.'"'] : ['', $href, $text, ''];

        return str_replace(['%class%', '%href%', '%text%', '%rel%'], $replace, $this->option('page_template'));
    }

    /**
     * @param int $page
     *
     * @return mixed|string
     */
    public function current(int $page) :string
    {
        return $this->generateSpan($this->option('css_active_class'), $page);
    }

    /**
     * @param TranslatorInterface $translator
     *
     * @return void
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return string
     */
    public function previousDisabled() :string
    {
        return $this->option('prev_message');
    }

    /**
     * @param int $page
     *
     * @return string
     */
    public function previousEnabled(int $page) :string
    {
        return str_replace(['%href%'], $this->generateRoute($page), $this->previousDisabled());
    }

    /**
     * @return string
     */
    public function nextDisabled() :string
    {
        return $this->option('next_message');
    }

    /**
     * @param int $page
     *
     * @return string
     */
    public function nextEnabled(int $page) :string
    {
        return str_replace(['%href%'], $this->generateRoute($page), $this->nextDisabled());
    }

    /**
     * @param string $domain
     *
     * @return void
     */
    public function init(string $domain)
    {
        $defaultOptions = $this->getDefaultOptions();

        $options = [
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
        ];

        $this->setOptions($options);
    }

    /**
     * @param string $class
     * @param int    $page
     *
     * @return string
     */
    private function generateSpan(string $class, $page): string
    {
        return str_replace(['%class%', '%text%'], [$class, $page], $this->option('span_template'));
    }
}
