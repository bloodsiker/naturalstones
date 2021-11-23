<?php

namespace MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class MediaImage
 *
 * @ORM\Entity()
 * @ORM\Table(name="media_image")
 * @ORM\HasLifecycleCallbacks
 *
 * @Vich\Uploadable
 */
class MediaImage extends AbstractMedia
{
    /**
     * @var string
     *
     * @ORM\Column(type="integer", nullable=true, options={"unsigned"=true})
     */
    protected $width;

    /**
     * @var string
     *
     * @ORM\Column(type="integer", nullable=true, options={"unsigned"=true})
     */
    protected $height;

    /**
     * Unmapped property to handle file uploads
     *
     * @Vich\UploadableField(mapping="media_image", fileNameProperty="path")
     */
    private $file;

    /**
     * Sets file
     *
     * @param File|null $file
     *
     * @throws \Exception
     */
    public function setFile(File $file = null)
    {
        $this->file = $file;
        if (null !== $file) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param string $width
     *
     * @return $this
     */
    public function setWidth(string $width = null)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @return string
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param string $height
     *
     * @return $this
     */
    public function setHeight(string $height = null)
    {
        $this->height = $height;

        return $this;
    }
}
