<?php

namespace ShortcodeBundle\Templating;

use ShortcodeBundle\Processor\ProcessorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Templating\Helper\Helper;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\TemplateWrapper;

class ShortcodeHelper extends Helper
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly Environment $twig,
    ) {
    }

    public function getName(): string
    {
        return 'shortcode';
    }

    /**
     * @param bool|list<string> $enabled
     *
     * @throws \Throwable
     */
    public function renderShortcodes(string $content, bool|array $enabled = true, string $mode = ''): string
    {
        $needWrapping = false;
        $definitions = $this->container->getParameter('shortcode.definitions');

        foreach ($definitions as $name => $definition) {
            if (isset($definition['exclude_mode']) && in_array($mode, $definition['exclude_mode'], true)) {
                return $content;
            }

            $template = $this->getShortcodeTemplate($definition, $mode);

            $content = preg_replace_callback(
                $definition['pattern'],
                function (array $matches) use ($template, &$needWrapping, $definition, $enabled, $name): string {
                    if (!$enabled || (is_array($enabled) && !in_array($name, $enabled, true))) {
                        return '';
                    }

                    if ($definition['wrap']) {
                        $needWrapping = true;
                    }

                    $data = $this->applyProcessor($definition, $matches);

                    return $template instanceof TemplateWrapper
                        ? $template->render(['shortcode_data' => $data])
                        : $this->vksprintf($template, $data);
                },
                $content
            );
        }

        return $this->maybeWrap($content, $needWrapping);
    }

    /**
     * @param list<string> $names
     *
     * @throws \Throwable
     */
    public function renderShortcodesOnly(string $content, array $names = [], string $mode = ''): string
    {
        foreach ($this->iterateNamedDefinitions($names) as $definition) {
            $template = $this->getShortcodeTemplate($definition, $mode);
            foreach ($this->getDefinitionMatches($content, $definition, $template) as $match => $replace) {
                $content = str_replace($match, $replace, $content);
            }
        }

        return $content;
    }

    /**
     * @param list<string> $names
     *
     * @throws \Throwable
     */
    public function renderShortcodesPure(string $content, array $names = [], string $mode = ''): string
    {
        $finalContent = '';
        foreach ($this->iterateNamedDefinitions($names) as $definition) {
            $template = $this->getShortcodeTemplate($definition, $mode);
            foreach ($this->getDefinitionMatches($content, $definition, $template) as $replace) {
                $finalContent .= $replace;
            }
        }

        return $finalContent;
    }

    /**
     * @param list<string> $names
     *
     * @return iterable<string, array<string, mixed>>
     */
    private function iterateNamedDefinitions(array $names): iterable
    {
        foreach ($this->container->getParameter('shortcode.definitions') as $name => $definition) {
            if (in_array($name, $names, true)) {
                yield $name => $definition;
            }
        }
    }

    /**
     * @param array<string, mixed> $definition
     */
    private function getShortcodeTemplate(array $definition, string $mode): TemplateWrapper|string
    {
        $modeKey = $mode ? 'template_' . $mode : 'template';

        try {
            return $this->twig->load($definition[$modeKey] ?? $definition['template']);
        } catch (LoaderError) {
            try {
                return $this->twig->load($definition['template']);
            } catch (LoaderError) {
                return $definition['template'];
            }
        }
    }

    /**
     * @param array<string, mixed> $definition
     *
     * @return array<string, string>
     *
     * @throws \Throwable
     */
    private function getDefinitionMatches(string $content, array $definition, TemplateWrapper|string $template): array
    {
        $needWrapping = false;
        $finalMatches = [];

        foreach ($definition['pattern'] as $pattern) {
            preg_match_all($pattern, $content, $matchesAll);
            if (0 === count($matchesAll[0])) {
                continue;
            }

            if ($definition['wrap']) {
                $needWrapping = true;
            }

            foreach ($this->transposeMatches($matchesAll) as $ind => $matches) {
                $data = $this->applyProcessor($definition, $matches);

                $finalContent = $template instanceof TemplateWrapper
                    ? $template->render(['shortcode_data' => $data])
                    : $this->vksprintf($template, $data);

                $finalContent = $this->maybeWrap($finalContent, $needWrapping);
                $finalMatches[$matchesAll[0][$ind]] = $finalContent;
            }
        }

        return $finalMatches;
    }

    /**
     * Convert preg_match_all's column-oriented results to row-oriented arrays.
     *
     * @param array<int|string, list<string>> $matchesAll
     *
     * @return list<array<int|string, string>>
     */
    private function transposeMatches(array $matchesAll): array
    {
        $rows = [];
        foreach ($matchesAll[0] as $ind => $_) {
            foreach ($matchesAll as $k => $v) {
                $rows[$ind][$k] = $v[$ind];
            }
        }

        return $rows;
    }

    /**
     * @param array<string, mixed>          $definition
     * @param array<int|string, mixed>      $matches
     *
     * @return array<int|string, mixed>
     */
    private function applyProcessor(array $definition, array $matches): array
    {
        if (!$definition['processor']) {
            return $matches;
        }

        /** @var ProcessorInterface $processor */
        $processor = $this->container->get($definition['processor']);

        return $processor->process($matches);
    }

    private function maybeWrap(string $content, bool $needWrapping): string
    {
        $wrapperTemplate = $this->container->getParameter('shortcode.wrapper.template');
        if (null === $wrapperTemplate) {
            return $content;
        }

        return $this->twig->load($wrapperTemplate)->render([
            'wrap' => $needWrapping,
            'content' => $content,
        ]);
    }

    /**
     * Like vsprintf, but accepts $args keys instead of order index.
     * Both numeric and strings matching /[a-zA-Z0-9_-]+/ are allowed.
     *
     * Example: vksprintf('y = %y$d, x = %x$1.1f', array('x' => 1, 'y' => 2))
     * Result:  'y = 2, x = 1.0'
     *
     * @param array<string, mixed>|object $args
     *
     * @see http://php.net/manual/en/function.vsprintf.php#110666
     */
    private function vksprintf(string $str, array|object $args): string
    {
        if (is_object($args)) {
            $args = get_object_vars($args);
        }

        $map = array_flip(array_keys($args));

        $newStr = preg_replace_callback(
            '/(^|[^%])%([a-zA-Z0-9_-]+)\$/',
            static fn ($m): string => $m[1] . '%' . ($map[$m[2]] + 1) . '$',
            $str
        );

        return vsprintf($newStr, $args);
    }
}