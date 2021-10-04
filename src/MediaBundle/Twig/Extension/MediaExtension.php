<?php

namespace MediaBundle\Twig\Extension;

use MediaBundle\Helper\MimeTypeHelper;

/**
 * Class MediaExtension
 */
class MediaExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getTests()
    {
        return array(
            new \Twig_SimpleTest('mediainstanceof', array($this, 'isMediaInstanceOf')),
        );
    }

    /**
     * @return array|\Twig_Filter[]
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('set_frame_size', array($this, 'setFrameSize')),
            new \Twig_SimpleFilter('file_extension', array($this, 'getFileExtension')),
            new \Twig_SimpleFilter('file_mime_icon_class', array($this, 'getFileMimeIconClass')),
        ];
    }

    /**
     * @param string     $code
     * @param string|int $width
     * @param string|int $height
     *
     * @return string
     */
    public function setFrameSize($code, $width = '100%', $height = '100%')
    {
        $newWidth   = is_numeric($width) ? $width.'px' : $width;
        $newHeight  = is_numeric($height) ? $height.'px' : $height;

        $code = preg_replace('/width="(.*?)"/i', 'width="'.$newWidth.'"', $code);
        $code = preg_replace('/height="(.*?)"/i', 'height="'.$newHeight.'"', $code);

        return $code;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function getFileExtension($path)
    {
        return strtolower(pathinfo($path, PATHINFO_EXTENSION));
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public static function getExtensionFile($path)
    {
        return strtolower(pathinfo($path, PATHINFO_EXTENSION));
    }

    /**
     * @param string $path
     * @param bool   $fixedWidth
     *
     * @return string
     */
    public function getFileMimeIconClass($path, $fixedWidth = false)
    {
        return MimeTypeHelper::getFontAwesomeIcon($path, $fixedWidth);
    }

    /**
     * Checks if $var is instance of $instance for media
     *
     * @param mixed $var
     * @param mixed $instance
     *
     * @return bool
     */
    public function isMediaInstanceOf($var, $instance)
    {
        return $var instanceof $instance;
    }
}