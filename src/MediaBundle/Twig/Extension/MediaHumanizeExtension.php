<?php
/*
 * This file is part filters of the MediaBundle package.
 *
 * Humanize filesize from bytes
 * Humanize duration from seconds
 * Humanize bitrate from bytes per seconds
 *
 */

namespace MediaBundle\Twig\Extension;

/**
 * Class MediaHumanizeExtension
 */
class MediaHumanizeExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('media_humanize_filesize', array($this, 'mediaHumanizeFilesizeFilter')),
            new \Twig_SimpleFilter('media_humanize_duration', array($this, 'mediaHumanizeDurationFilter')),
            new \Twig_SimpleFilter('media_humanize_bitrate', array($this, 'mediaHumanizeBitRateFilter')),
            new \Twig_SimpleFilter('media_humanize_byteconvert', array($this, 'mediaByteConvertFilter')),
        );
    }

    /**
     * @param string|int $filesize
     * @param int        $mod
     *
     * @return string
     */
    public function mediaHumanizeFilesizeFilter($filesize, $mod = 1024)
    {
        $units = explode(' ', 'B KB MB GB TB PB');
        for ($i = 0; $filesize > $mod; $i++) {
            $filesize /= $mod;
        }

        return round($filesize, 2).' '.$units[$i];
    }

    /**
     * @param int $bitrate
     *
     * @return string
     */
    public function mediaHumanizeBitRateFilter($bitrate)
    {
        $humanBitRate = $this->mediaHumanizeFilesizeFilter($bitrate);

        return $humanBitRate ? $humanBitRate.'/s' : '';
    }

    /**
     * @param int    $seconds
     * @param string $separator
     *
     * @return string
     */
    public function mediaHumanizeDurationFilter($seconds = 0, $separator = ':')
    {
        return sprintf(
            "%02d%s%02d%s%02d%s",
            floor($seconds/3600),
            $separator === ':' ? $separator : 'h ',
            ($seconds/60)%60,
            $separator === ':' ? $separator : 'm ',
            $seconds%60,
            $separator === ':' ? '' : 's '
        );
    }

    /**
     * @param string $from
     *
     * @return mixed
     */
    public function mediaByteConvertFilter($from)
    {
        $number = substr($from, 0, -2);
        switch (strtoupper(substr($from, -2))) {
            case "KB":
                return $number * 1024;
            case "MB":
                return $number * pow(1024, 2);
            case "GB":
                return $number * pow(1024, 3);
            case "TB":
                return $number * pow(1024, 4);
            case "PB":
                return $number * pow(1024, 5);
            default:
                return $from;
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'media_humanize';
    }
}