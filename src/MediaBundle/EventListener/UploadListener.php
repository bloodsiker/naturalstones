<?php

namespace MediaBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use MediaBundle\Entity\MediaImage;
use MediaBundle\Entity\MediaImageTranslation;
use MediaBundle\Services\MediaImageService;
use MediaBundle\Services\MediaVideoService;
use Oneup\UploaderBundle\Event\PostUploadEvent;
use Oneup\UploaderBundle\Uploader\File\GaufretteFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UploadListener
{
    private const SUPPORTED_LOCALES = ['uk', 'ru'];

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly MediaImageService $imageService,
        private readonly MediaVideoService $videoService,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function onPostUploadFile(PostUploadEvent $event): array
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        $response['success'] = true;
        $response['files'] = $this->getFileUploadMetadata($event->getFile(), $request->get('storage'));

        return $response;
    }

    /**
     * @return array<string, mixed>
     */
    public function onPostUploadImageFile(PostUploadEvent $event): array
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        $metadata = $this->getFileUploadMetadata(
            $event->getFile(),
            $request->get('storage'),
            findImageDuplicate: true
        );

        $image = $metadata['object'] instanceof MediaImage
            ? $metadata['object']
            : $this->createImageFromMetadata($metadata, $request->files->get($request->get('field')));

        $response['success'] = true;
        $response['files'] = $metadata;
        $response['image'] = $image->getId();

        return $response;
    }

    /**
     * @param array<string, mixed> $metadata
     */
    private function createImageFromMetadata(array $metadata, ?File $file): MediaImage
    {
        $image = new MediaImage();
        $image->setSize($metadata['size']);
        $image->setHash($metadata['hash']);
        $image->setWidth($metadata['width']);
        $image->setHeight($metadata['height']);
        $image->setMimeType($metadata['mimeType']);
        $image->setTempPath($metadata['tempPath']);
        $image->setCreatedBy($this->tokenStorage->getToken()->getUser());

        $image->setPath($this->imageService->moveUploadedFile($image));

        $origFileName = $file?->getClientOriginalName() ?? '';
        foreach (self::SUPPORTED_LOCALES as $locale) {
            $translation = new MediaImageTranslation();
            $translation->setLocale($locale);
            $translation->setTitle($origFileName);
            $translation->setTranslatable($translation);
            $image->addTranslation($translation);
        }

        $this->em->persist($image);
        $this->em->flush();

        return $image;
    }

    /**
     * @return array<string, mixed>
     */
    private function getFileUploadMetadata(
        File|GaufretteFile|null $file,
        ?string $storage,
        bool $findImageDuplicate = false,
    ): array {
        $filePath = match (true) {
            $file instanceof File => $file->getRealPath(),
            $file instanceof GaufretteFile => $file->getKey(),
            default => null,
        };

        if (!$filePath || null === $file) {
            return ['tempPath' => null];
        }

        $md5File = md5_file($filePath);
        $mimeType = $file->getMimeType();

        $data = [
            'tempPath' => $storage . '|' . $filePath,
            'mimeType' => $mimeType,
            'name' => $file->getBaseName(),
            'size' => $file->getSize(),
            'hash' => $md5File,
            'object' => null,
        ];

        if ($findImageDuplicate) {
            $data['object'] = $this->em->getRepository(MediaImage::class)
                ->findOneBy(['hash' => $md5File], ['createdAt' => 'DESC']);
        }

        if (false !== strpos($mimeType, 'image/')) {
            [$data['width'], $data['height']] = getimagesize($filePath);
        } elseif (false !== strpos($mimeType, 'video/')) {
            $data += $this->extractVideoMetadata($filePath);
        }

        return $data;
    }

    /**
     * @return array<string, int>
     */
    private function extractVideoMetadata(string $filePath): array
    {
        $metadata = $this->videoService->getVideoMetadata($filePath);
        $extracted = [];

        if (!empty($metadata->format->bit_rate)) {
            $extracted['bitrate'] = (int) $metadata->format->bit_rate;
        }

        if (!empty($metadata->streams[0]) && 'video' === $metadata->streams[0]->codec_type) {
            $extracted['width'] = (int) $metadata->streams[0]->width;
            $extracted['height'] = (int) $metadata->streams[0]->height;
            $extracted['duration'] = (int) $metadata->streams[0]->duration;
        }

        return $extracted;
    }
}