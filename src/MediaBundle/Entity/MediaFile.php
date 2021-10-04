<?php

namespace MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class MediaFile
 *
 * @ORM\Entity()
 * @ORM\Table(name="media_file")
 * @ORM\HasLifecycleCallbacks
 *
 * @Vich\Uploadable
 */
class MediaFile extends AbstractMedia
{
    /**
     * Unmapped property to handle file uploads
     *
     * @Vich\UploadableField(mapping="media_file", fileNameProperty="path")
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
}
