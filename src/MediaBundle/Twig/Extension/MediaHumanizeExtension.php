<?php

/*
 * Humanize filesize from bytes, duration from seconds, bitrate from bytes per seconds.
 */

namespace MediaBundle\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MediaHumanizeExtension extends AbstractExtension
{
    private const FILESIZE_UNITS = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

    /**
     * @return list<TwigFilter>
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('media_humanize_filesize', $this->mediaHumanizeFilesizeFilter(...)),
            new TwigFilter('media_humanize_duration', $this->mediaHumanizeDurationFilter(...)),
            new TwigFilter('media_humanize_bitrate', $this->mediaHumanizeBitRateFilter(...)),
            new TwigFilter('media_humanize_byteconvert', $this->mediaByteConvertFilter(...)),
        ];
    }

    public function mediaHumanizeFilesizeFilter(string|int|float $filesize, int $mod = 1024): string
    {
        $filesize = (float) $filesize;
        $i = 0;
        while ($filesize > $mod) {
            $filesize /= $mod;
            ++$i;
        }

        return round($filesize, 2) . ' ' . self::FILESIZE_UNITS[$i];
    }

    public function mediaHumanizeBitRateFilter(int $bitrate): string
    {
        $humanBitRate = $this->mediaHumanizeFilesizeFilter($bitrate);

        return $humanBitRate ? $humanBitRate . '/s' : '';
    }

    public function mediaHumanizeDurationFilter(int $seconds = 0, string $separator = ':'): string
    {
        $colonStyle = ':' === $separator;

        return sprintf(
            '%02d%s%02d%s%02d%s',
            floor($seconds / 3600),
            $colonStyle ? $separator : 'h ',
            ($seconds / 60) % 60,
            $colonStyle ? $separator : 'm ',
            $seconds % 60,
            $colonStyle ? '' : 's '
        );
    }

    public function mediaByteConvertFilter(string $from): string|int|float
    {
        $number = (float) substr($from, 0, -2);

        return match (strtoupper(substr($from, -2))) {
            'KB' => $number * 1024,
            'MB' => $number * 1024 ** 2,
            'GB' => $number * 1024 ** 3,
            'TB' => $number * 1024 ** 4,
            'PB' => $number * 1024 ** 5,
            default => $from,
        };
    }

    public function getName(): string
    {
        return 'media_humanize';
    }
}