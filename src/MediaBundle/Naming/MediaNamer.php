<?php

namespace MediaBundle\Naming;

use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\NamerInterface;
use Vich\UploaderBundle\Util\Transliterator;

class MediaNamer implements NamerInterface
{
    private ?string $pathImage = null;

    public function __construct(
        private readonly Transliterator $transliterator,
    ) {
    }

    public function name($object, PropertyMapping $mapping): string
    {
        $file = $mapping->getFile($object);
        $name = uniqid() . '_' . $this->transliterator->transliterate($file->getClientOriginalName());

        return $this->fillPath($name);
    }

    public function setPathImage(string $pathImage): self
    {
        $this->pathImage = $pathImage;

        return $this;
    }

    protected function fillPath(string $name): string
    {
        if (null === $this->pathImage) {
            return '/img_tmp/' . $name;
        }

        [$year, $month] = explode('/', (new \DateTime())->format('Y/m'));

        return str_replace(['[YEAR]', '[MONTH]', '[FILE]'], [$year, $month, $name], $this->pathImage);
    }
}