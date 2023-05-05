<?php

namespace MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class MediaVideo
 *
 * @ORM\Entity()
 * @ORM\Table(name="media_video")
 * @ORM\HasLifecycleCallbacks
 *
 * @Vich\Uploadable
 */
class MediaVideo extends AbstractMedia
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $thumb;

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

    public function __construct()
    {
        parent::__construct();
        $this->updatedAt = new \DateTime('now');
    }

    /**
     * @return string
     */
    public function getThumb()
    {
        return $this->thumb;
    }

    /**
     * @param  string  $thumb
     *
     * @return $this
     */
    public function setThumb($thumb)
    {
        $this->thumb = $thumb;

        return $this;
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
