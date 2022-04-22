<?php

namespace ShortcodeBundle\Templating;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * Class ShortcodeHelper
 */
class ShortcodeHelper extends Helper
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->twig = $this->container->get('twig');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'shortcode';
    }

    /**
     * @param string          $content
     * @param array|bool|true $enabled
     * @param string          $mode
     *
     * @return string
     *
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function renderShortcodes($content, $enabled = true, $mode = '')
    {
        $needWrapping = false;

        $definitions = $this->container->getParameter('shortcode.definitions');

        foreach ($definitions as $name => $definition) {
            if (isset($definition['exclude_mode']) && in_array($mode, $definition['exclude_mode'])) {
                return $content;
            }

            $template = $this->getShortcodeTemplate($definition, $mode);

            $content = preg_replace_callback($definition['pattern'], function ($matches) use ($template, &$needWrapping, $definition, $enabled, $name) {
                if (!$enabled || is_array($enabled) && !in_array($name, $enabled)) {
                    return '';
                }

                if ($definition['wrap']) {
                    $needWrapping = true;
                }

                if ($definition['processor']) {
                    /** @var \ShortcodeBundle\Processor\ProcessorInterface $processor */
                    $processor = $this->container->get($definition['processor']);
                    $data = $processor->process($matches);
                } else {
                    $data = $matches;
                }

                if ($template instanceof \Twig_Template) {
                    return $template->render(['shortcode_data' => $data]);
                } else {
                    return $this->vksprintf($template, $data);
                }
            }, $content);
        }

        if ($this->container->getParameter('shortcode.wrapper.template') !== null) {
            $wrapperTemplate = $this->twig->loadTemplate($this->container->getParameter('shortcode.wrapper.template'));

            $content = $wrapperTemplate->render(array(
                'wrap' => $needWrapping,
                'content' => $content,
            ));
        }

        return $content;
    }

    /**
     * @param string $content
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
    public function renderShortcodesOnly($content, $names = [], $mode = '')
    {
        $definitions = $this->container->getParameter('shortcode.definitions');

        foreach ($definitions as $name => $definition) {
            if (!in_array($name, $names)) {
                continue;
            }

            $template = $this->getShortcodeTemplate($definition, $mode);
            $definitionMatches = $this->getDefinitionMatches($content, $definition, $template);
            foreach ($definitionMatches as $match => $replace) {
                $content = str_replace($match, $replace, $content);
            }
        }

        return $content;
    }

    /**
     * @param string $content
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
    public function renderShortcodesPure($content, $names = [], $mode = '')
    {
        $definitions = $this->container->getParameter('shortcode.definitions');

        $finalContent = '';
        foreach ($definitions as $name => $definition) {
            if (!in_array($name, $names)) {
                continue;
            }

            $template = $this->getShortcodeTemplate($definition, $mode);
            $definitionMatches = $this->getDefinitionMatches($content, $definition, $template);
            foreach ($definitionMatches as $match => $replace) {
                $finalContent .= $replace;
            }
        }

        return $finalContent;
    }

    /**
     * @param array  $definition
     * @param string $mode
     *
     * @return \Twig_Template|\Twig_TemplateInterface
     *
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    private function getShortcodeTemplate($definition, $mode)
    {
        try {
            $template = $this->twig->loadTemplate($definition['template'.($mode?'_'.$mode:'')]);
        } catch (\Twig_Error_Loader $e) {
            try {
                $template = $this->twig->loadTemplate($definition['template']);
            } catch (\Twig_Error_Loader $e) {
                $template = $definition['template'];
            }
        }

        return $template;
    }

    /**
     * @param string $content
     * @param array  $definition
     * @param string $template
     *
     * @return array
     *
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    private function getDefinitionMatches($content, $definition, $template)
    {
        $needWrapping = false;

        $finalMatches = [];
        foreach ($definition['pattern'] as $pattern) {
            preg_match_all($pattern, $content, $matchesAll);
            if (count($matchesAll[0]) > 0) {
                if ($definition['wrap']) {
                    $needWrapping = true;
                }

                $matchesAllAdapted = [];
                foreach ($matchesAll[0] as $ind => $vals) {
                    foreach ($matchesAll as $k => $v) {
                        $matchesAllAdapted[$ind][$k] = $v[$ind];
                    }
                }

                foreach ($matchesAllAdapted as $ind => $matches) {
                    if ($definition['processor']) {
                        /** @var \ShortcodeBundle\Processor\ProcessorInterface $processor */
                        $processor = $this->container->get($definition['processor']);
                        $data = $processor->process($matches);
                    } else {
                        $data = $matches;
                    }

                    if ($template instanceof \Twig_Template) {
                        $finalContent = $template->render(['shortcode_data' => $data]);
                    } else {
                        $finalContent = $this->vksprintf($template, $data);
                    }

                    if ($this->container->getParameter('shortcode.wrapper.template') !== null) {
                        $wrapperTemplate = $this->twig->loadTemplate($this->container->getParameter('shortcode.wrapper.template'));

                        $finalContent = $wrapperTemplate->render(array(
                            'wrap' => $needWrapping,
                            'content' => $finalContent,
                        ));
                    }

                    $finalMatches[$matchesAll[0][$ind]] = $finalContent;
                }
            }
        }

        return $finalMatches;
    }

    /**
     * Like vsprintf, but accepts $args keys instead of order index.
     * Both numeric and strings matching /[a-zA-Z0-9_-]+/ are allowed.
     *
     * Example: vskprintf('y = %y$d, x = %x$1.1f', array('x' => 1, 'y' => 2))
     * Result:  'y = 2, x = 1.0'
     * $args also can be object, then it's properties are retrieved
     * using get_object_vars().
     *
     * '%s' without argument name works fine too. Everything vsprintf() can do
     * is supported.
     *
     * @author Josef Kufner <jkufner(at)gmail.com>
     *
     * @link http://php.net/manual/en/function.vsprintf.php#110666
     *
     * @param string $str
     * @param mixed  $args
     *
     * @return string
     */
    private function vksprintf($str, $args)
    {
        if (is_object($args)) {
            $args = get_object_vars($args);
        }

        $map = array_flip(array_keys($args));

        $newStr = preg_replace_callback('/(^|[^%])%([a-zA-Z0-9_-]+)\$/', function ($m) use ($map) {
            return $m[1].'%'.($map[$m[2]] + 1).'$';
        }, $str);

        return vsprintf($newStr, $args);
    }
}
