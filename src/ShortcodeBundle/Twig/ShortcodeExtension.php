<?php

namespace ShortcodeBundle\Twig;

use ShortcodeBundle\Templating\ShortcodeHelper;

/**
 * Class ShortcodeExtension
 */
class ShortcodeExtension extends \Twig_Extension
{
    /**
     * @var ShortcodeHelper
     */
    private $helper;

    /**
     * @param ShortcodeHelper $helper
     */
    public function __construct(ShortcodeHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'shortcode_extension';
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('shortcode', [$this, 'shortcodeFilter'], ['is_safe' => ['html']]),
            new \Twig_SimpleFilter('shortcode_only', [$this, 'shortcodeOnlyFilter'], ['is_safe' => ['html']]),
            new \Twig_SimpleFilter('shortcode_pure', [$this, 'shortcodePureFilter'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Replaces shortcode entities with corresponding content.
     *
     * @param string     $content Content for filtering
     * @param bool|array $enabled
     * @param string     $mode
     *
     * @return string
     *
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function shortcodeFilter($content, $enabled = true, $mode = '')
    {
        return $this->helper->renderShortcodes($content, $enabled, $mode);
    }


    /**
     * Replaces shortcode entities with corresponding content.
     *
     * @param string $content Content for filtering
     * @param array  $names
     * @param string $mode
     *
     * @return string
     *
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function shortcodeOnlyFilter($content, $names = [], $mode = '')
    {
        return $this->helper->renderShortcodesOnly($content, $names, $mode);
    }

    /**
     * Replaces shortcode entities with corresponding content.
     *
     * @param string $content Content for filtering
     * @param array  $names
     * @param string $mode
     *
     * @return string
     *
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function shortcodePureFilter($content, $names = [], $mode = '')
    {
        return $this->helper->renderShortcodesPure($content, $names, $mode);
    }
}
