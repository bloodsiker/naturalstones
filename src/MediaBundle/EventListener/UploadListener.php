<?php

namespace MediaBundle\EventListener;

use MediaBundle\Entity\MediaImage;
use MediaBundle\Entity\MediaImageTranslation;
use MediaBundle\Services\MediaImageService;
use MediaBundle\Services\MediaVideoService;
use Oneup\UploaderBundle\Event\PostUploadEvent;
use Oneup\UploaderBundle\Uploader\File\GaufretteFile;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class UploadListener
 */
class UploadListener implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var MediaVideoService
     */
    private $videoService;

    /**
     * @var MediaImageService
     */
    private $imageService;

    /**
     * @param MediaVideoService $videoService
     */
    public function setVideoService(MediaVideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    /**
     * @param MediaImageService $imageService
     */
    public function setImageService(MediaImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * @param PostUploadEvent $event
     *
     * @return \Oneup\UploaderBundle\Uploader\Response\ResponseInterface
     */
    public function onPostUploadFile(PostUploadEvent $event)
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        $response['success'] = true;
        $response['files'] = $this->getFileUploadMetadata(
            $event->getFile(),
            $request->get('storage')
        );

        return $response;
    }

    /**
     * @param PostUploadEvent $event
     *
     * @return \Oneup\UploaderBundle\Uploader\Response\ResponseInterface
     *
     * @throws \MediaBundle\Exception\MediaException
     */
    public function onPostUploadImageFile(PostUploadEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        $metadata = $this->getFileUploadMetadata(
            $event->getFile(),
            $request->get('storage'),
            true
        );

        if (!$metadata['object'] instanceof MediaImage) {
            $image = new MediaImage();

            $image->setSize($metadata['size']);
            $image->setHash($metadata['hash']);
            $image->setWidth($metadata['width']);
            $image->setHeight($metadata['height']);
            $image->setMimeType($metadata['mimeType']);
            $image->setTempPath($metadata['tempPath']);
            $image->setCreatedBy(
                $this->container
                    ->get('security.token_storage')
                    ->getToken()
                    ->getUser()
            );

            $file = $request->files->get($request->get('field'));
            $origFileName = $file->getClientOriginalName();

            $path = $this->imageService->moveUploadedFile($image);
            $image->setPath($path);

            $imageUK = new MediaImageTranslation();
            $imageUK->setLocale('uk');
            $imageUK->setTitle($origFileName);
            $imageUK->setTranslatable($imageUK);

            $imageRU = new MediaImageTranslation();
            $imageRU->setLocale('ru');
            $imageRU->setTitle($origFileName);
            $imageRU->setTranslatable($imageRU);

            $image->addTranslation($imageUK);
            $image->addTranslation($imageRU);

            $em = $this->container->get('doctrine.orm.entity_manager');
            $em->persist($image);
            $em->flush();
        } else {
            $image = $metadata['object'];
        }

        $response['success']    = true;
        $response['files']      = $metadata;
        $response['image']      = $image->getId();

        return $response;
    }

    /**
     * @param GaufretteFile|File $file
     * @param string             $storage
     * @param bool               $findImageDuplicate
     *
     * @return array
     */
    private function getFileUploadMetadata($file, $storage, $findImageDuplicate = false)
    {
        $data = ['tempPath' => null];
        if ($file instanceof File) {
            $filePath = $file->getRealPath();
//            $mimeType = $file->getMimeType();
        } elseif ($file instanceof GaufretteFile) {
            $filePath = $file->getKey();
        } else {
            $filePath =  null;
        }

        if ($filePath) {
            $md5File = md5_file($filePath);

            $data = [
                'tempPath'  => $storage.'|'.$filePath,
                'mimeType'  => $file->getMimeType(),
                'name'      => $file->getBaseName(),
                'size'      => $file->getSize(),
                'hash'      => $md5File,
                'object'    => null,
            ];

            if (true === $findImageDuplicate) {
                $data['object'] = $this->container
                    ->get('doctrine')
                    ->getRepository(MediaImage::class)
                    ->findOneBy(['hash' => $md5File], ['createdAt' => 'DESC'])
                ;
            }

            if (false !== strpos($file->getMimeType(), 'image/')) {
                list($width, $height) = getimagesize($filePath);

                $data['width'] = $width;
                $data['height'] = $height;
            } elseif (false !== strpos($file->getMimeType(), 'video/')) {
                $metadata = $this->videoService->getVideoMetadata($filePath);

                if (!empty($metadata->format->bit_rate)) {
                    $data['bitrate'] = (int) $metadata->format->bit_rate;
                }

                if (!empty($metadata->streams[0]) && $metadata->streams[0]->codec_type === 'video') {
                    $data['width']     = (int) $metadata->streams[0]->width;
                    $data['height']    = (int) $metadata->streams[0]->height;
                    $data['duration']  = (int) $metadata->streams[0]->duration;
                }
            }
        }

        return $data;
    }
}