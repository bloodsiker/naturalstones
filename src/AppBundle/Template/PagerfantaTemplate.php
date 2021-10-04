<?php

namespace AppBundle\Template;

use AppBundle\Services\SaveStateValue;
use Pagerfanta\View\Template\DefaultTemplate;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class PagerfantaTemplate
 */
class PagerfantaTemplate extends DefaultTemplate
{
    static protected $defaultOptions = [
        'prev_message'       => '<span class="pagination-prev"><a href="%href%">%prev_text%</a></span>',
        'next_message'       => '<span class="pagination-next"><a href="%href%">%next_text%</a></span>',
        'css_disabled_class' => '',
        'css_dots_class'     => 'nav_ext',
        'css_current_class'  => 'pagination-current',
        'dots_text'          => '...',
        'container_template' => '<div class="pagination">%prev%<span class="navigation">%pages%</span>%next%</div>',
        'page_template'      => '<a href="%href%"%rel%>%text%</a>',
        'span_template'      => '<span class="%class%">%text%</span>',
        'rel_previous'        => 'prev',
        'rel_next'            => 'next',
    ];

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
    public function pageWithText($page, $text, $rel = null)
    {
        $search = array('%href%', '%text%', '%rel%');

        $href = $this->generateRoute($page);
        $replace = $rel ? [$href, $text, ' rel="'.$rel.'"'] : [$href, $text, ''];

        return str_replace($search, $replace, $this->option('page_template'));
    }

    /**
     * @param string      $href
     * @param string      $text
     * @param string|null $rel
     *
     * @return mixed
     */
    public function extraPageWithText($href, $text, $rel = null)
    {
        $search = array('%href%', '%text%', '%rel%');

        $replace = $rel ? [$href, $text, ' rel="'.$rel.'"'] : [$href, $text, ''];

        return str_replace($search, $replace, $this->option('page_template'));
    }

    /**
     * @param int $page
     *
     * @return mixed|string
     */
    public function current($page)
    {
        return $this->generateSpan($this->option('css_current_class'), $page);
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
    public function previousDisabled()
    {
        return $this->option('prev_message');
    }

    /**
     * @param int $page
     *
     * @return string
     */
    public function previousEnabled($page)
    {
        return str_replace(['%href%'], $this->generateRoute($page), $this->previousDisabled());
    }

    /**
     * @return string
     */
    public function nextDisabled()
    {
        return $this->option('next_message');
    }

    /**
     * @param int $page
     *
     * @return string
     */
    public function nextEnabled($page)
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
        $options = [
            'prev_message' => str_replace(
                '%prev_text%',
                $this->translator->trans('app.pager.previous', [], $domain),
                self::$defaultOptions['prev_message']
            ),
            'next_message' => str_replace(
                '%next_text%',
                $this->translator->trans('app.pager.next', [], $domain),
                self::$defaultOptions['next_message']
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
    private function generateSpan($class, $page)
    {
        $search = array('%class%', '%text%', '%href%');
        $replace = array($class, $page, $this->generateRoute($page));

        return str_replace($search, $replace, $this->option('span_template'));
    }
}