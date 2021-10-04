<?php

namespace MediaBundle\Naming;

use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\NamerInterface;
use Vich\UploaderBundle\Util\Transliterator;

/**
 * Class MediaNamer
 */
class MediaNamer implements NamerInterface
{
    /**
     * image path
     *
     * @var $pathImage
     */
    private $pathImage = null;

    /**
     * @param object          $object
     * @param PropertyMapping $mapping
     *
     * @return string
     */
    public function name($object, PropertyMapping $mapping): string
    {
        $file = $mapping->getFile($object);
        $name = $file->getClientOriginalName();
        $name = uniqid().'_'.Transliterator::transliterate($name);

        return $this->fillPath($name);
    }

    /**
     * @param string $pathImage
     *
     * @return $this
     */
    public function setPathImage(string $pathImage): self
    {
        $this->pathImage = $pathImage;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function fillPath($name): string
    {
        $date = new \DateTime();
        list($year, $month) = explode('/', $date->format('Y/m'));
        if (null !== $this->pathImage) {
            $pattern = ['[YEAR]', '[MONTH]', '[FILE]'];
            $replace = [$year, $month, $name];
            $path = str_replace($pattern, $replace, $this->pathImage);
        } else {
            $path = '/img_tmp/'.$name;
        }

        return $path;
    }
}