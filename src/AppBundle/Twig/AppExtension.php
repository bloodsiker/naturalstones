<?php

namespace AppBundle\Twig;

use Sonata\BlockBundle\Model\Block;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

class AppExtension extends AbstractExtension
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly RouterInterface $router,
    ) {
    }

    public function getName(): string
    {
        return 'app_extension';
    }

    /**
     * @return list<TwigFunction>
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('block_ajax_url', $this->blockAjaxUrl(...)),
            new TwigFunction('replace_highlight', $this->replaceHighlight(...)),
            new TwigFunction('is_constrain', $this->isConstrain(...)),
        ];
    }

    /**
     * @return list<TwigTest>
     */
    public function getTests(): array
    {
        return [
            new TwigTest('instanceof', $this->isInstanceOf(...)),
        ];
    }

    /**
     * @return list<TwigFilter>
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('file_size_humanize', $this->fileSizeHumanize(...)),
            new TwigFilter('date_humanize', $this->dateHumanize(...)),
            new TwigFilter('date_time_humanize', $this->dateTimeHumanize(...)),
            new TwigFilter('url_decode', $this->urlDecode(...)),
        ];
    }

    public function replaceHighlight(string $value, string $replace): string
    {
        return preg_replace("/$replace/iu", '<span class="highlight">' . $replace . '</span>', $value);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function blockAjaxUrl(Block $block, array $parameters = []): string
    {
        $parameters = array_merge($parameters, [
            'blockId' => $block->getId(),
            'blockType' => $block->getType(),
        ]);

        return $this->router->generate('block__ajax', $parameters);
    }

    public function isInstanceOf(mixed $var, string $instance): bool
    {
        return $var instanceof $instance;
    }

    public function isConstrain(int|string|null $id, ?string $string): ?string
    {
        if (null === $id || null === $string) {
            return null;
        }

        return in_array($id, explode(',', $string), false) ? 'checked' : null;
    }

    public function fileSizeHumanize(int $size, string $delimiter = ''): string
    {
        $prefix = ['b', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb'];

        $counter = 0;
        while (($size / 1024) > 1) {
            $size /= 1024;
            ++$counter;
        }

        return sprintf('%.2f%s%s', $size, $delimiter, $prefix[$counter]);
    }

    public function urlDecode(string $url): string
    {
        return urldecode($url);
    }

    /**
     * @throws \Exception
     */
    public function dateHumanize(\DateTime $datetime, string $formatDay = 'd MMMM'): string
    {
        $now = self::getNow();
        $date = $datetime->format('d.m.Y');

        if ($date === $now->format('d.m.Y')) {
            return $this->translator->trans('app.datetime.today', [], 'AppBundle');
        }

        if ($date === $now->sub(new \DateInterval('PT24H'))->format('d.m.Y')) {
            return $this->translator->trans('app.datetime.yesterday', [], 'AppBundle');
        }

        return datefmt_format_object($datetime, $formatDay);
    }

    /**
     * @throws \Exception
     */
    public function dateTimeHumanize(
        \DateTime $datetime,
        string $formatDay = 'd MMMM',
        string $formatTime = ', HH:mm',
    ): string {
        return $this->dateHumanize($datetime, $formatDay) . datefmt_format_object($datetime, $formatTime);
    }

    /**
     * @throws \Exception
     */
    private static function getNow(): \DateTime
    {
        $now = new \DateTime('now');
        $now->add(new \DateInterval('PT' . (60 - (int) $now->format('s')) . 'S'));

        return $now;
    }
}