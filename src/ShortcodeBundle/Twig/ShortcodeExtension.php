<?php

namespace ShortcodeBundle\Twig;

use ShortcodeBundle\Templating\ShortcodeHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ShortcodeExtension extends AbstractExtension
{
    public function __construct(
        private readonly ShortcodeHelper $helper,
    ) {
    }

    public function getName(): string
    {
        return 'shortcode_extension';
    }

    /**
     * @return list<TwigFilter>
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('shortcode', $this->shortcodeFilter(...), ['is_safe' => ['html']]),
            new TwigFilter('shortcode_only', $this->shortcodeOnlyFilter(...), ['is_safe' => ['html']]),
            new TwigFilter('shortcode_pure', $this->shortcodePureFilter(...), ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param bool|list<string> $enabled
     *
     * @throws \Throwable
     */
    public function shortcodeFilter(string $content, bool|array $enabled = true, string $mode = ''): string
    {
        return $this->helper->renderShortcodes($content, $enabled, $mode);
    }

    /**
     * @param list<string> $names
     *
     * @throws \Throwable
     */
    public function shortcodeOnlyFilter(string $content, array $names = [], string $mode = ''): string
    {
        return $this->helper->renderShortcodesOnly($content, $names, $mode);
    }

    /**
     * @param list<string> $names
     *
     * @throws \Throwable
     */
    public function shortcodePureFilter(string $content, array $names = [], string $mode = ''): string
    {
        return $this->helper->renderShortcodesPure($content, $names, $mode);
    }
}