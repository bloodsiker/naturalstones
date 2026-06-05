<?php

namespace MediaBundle\Twig\Extension;

use MediaBundle\Helper\MimeTypeHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigTest;

class MediaExtension extends AbstractExtension
{
    /**
     * @return list<TwigTest>
     */
    public function getTests(): array
    {
        return [
            new TwigTest('mediainstanceof', $this->isMediaInstanceOf(...)),
        ];
    }

    /**
     * @return list<TwigFilter>
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('set_frame_size', $this->setFrameSize(...)),
            new TwigFilter('file_extension', $this->getFileExtension(...)),
            new TwigFilter('file_mime_icon_class', $this->getFileMimeIconClass(...)),
        ];
    }

    public function setFrameSize(string $code, string|int $width = '100%', string|int $height = '100%'): string
    {
        $newWidth = is_numeric($width) ? $width . 'px' : $width;
        $newHeight = is_numeric($height) ? $height . 'px' : $height;

        $code = preg_replace('/width="(.*?)"/i', 'width="' . $newWidth . '"', $code);

        return preg_replace('/height="(.*?)"/i', 'height="' . $newHeight . '"', $code);
    }

    public function getFileExtension(string $path): string
    {
        return self::getExtensionFile($path);
    }

    public static function getExtensionFile(string $path): string
    {
        return strtolower(pathinfo($path, PATHINFO_EXTENSION));
    }

    public function getFileMimeIconClass(string $path, bool $fixedWidth = false): string
    {
        return MimeTypeHelper::getFontAwesomeIcon($path, $fixedWidth);
    }

    public function isMediaInstanceOf(mixed $var, string $instance): bool
    {
        return $var instanceof $instance;
    }
}