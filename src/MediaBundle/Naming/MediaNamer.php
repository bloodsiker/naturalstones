<?php

namespace MediaBundle\Naming;

use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\NamerInterface;
use Vich\UploaderBundle\Util\Transliterator;

class MediaNamer implements NamerInterface
{
    private $pathImage = null;
    private Transliterator $transliterator;

    public function __construct(Transliterator $transliterator)
    {
        $this->transliterator = $transliterator;
    }

    public function name($object, PropertyMapping $mapping): string
    {
        $file = $mapping->getFile($object);
        $name = $file->getClientOriginalName();
        $name = uniqid().'_'.$this->transliterator->transliterate($name);

        return $this->fillPath($name);
    }

    public function setPathImage(string $pathImage): self
    {
        $this->pathImage = $pathImage;

        return $this;
    }

    protected function fillPath($name): string
    {
        $date = new \DateTime();
        [$year, $month] = explode('/', $date->format('Y/m'));
        if (null !== $this->pathImage) {
            $path = str_replace(['[YEAR]', '[MONTH]', '[FILE]'], [$year, $month, $name], $this->pathImage);
        } else {
            $path = '/img_tmp/'.$name;
        }

        return $path;
    }
}