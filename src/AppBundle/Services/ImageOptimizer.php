<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Intervention\Image\ImageManager;
use MediaBundle\Entity\MediaImage;
use Symfony\Component\HttpFoundation\RequestStack;

class ImageOptimizer
{
    private const S_800 = 800;
    private const S_750 = 750;

    private ImageManager $imagine;

    private EntityManagerInterface $em;
    private RequestStack $requestStack;
    private string $imagineWebRoot;

    public function __construct(
        EntityManagerInterface $em,
        RequestStack $requestStack,
        string $imagineWebRoot
    ) {
        $this->em = $em;
        $this->requestStack = $requestStack;
        $this->imagineWebRoot = $imagineWebRoot;
        $this->imagine = new ImageManager();
    }

    public function resize(MediaImage $object): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request || !$request->get('pcode') || !$request->get('field_name')) {
            return;
        }

        $sizes = $this->getSizes();
        $path  = $this->imagineWebRoot . $object->getPath();

        if (!array_key_exists($request->get('pcode'), $sizes)) {
            return;
        }

        $adminCodes = $sizes[$request->get('pcode')];
        if (!array_key_exists($request->get('field_name'), $adminCodes)) {
            return;
        }

        $width = $adminCodes[$request->get('field_name')]['width'];

        $this->imagine->make($path)->resize($width, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->save($path);

        $file = $object->getFile();
        [$w, $h] = getimagesize($path);
        $object->setWidth($w ?: null);
        $object->setHeight($h ?: null);
        $object->setSize($file->getSize());

        $this->em->persist($object);
        $this->em->flush();
    }

    protected function getSizes(): array
    {
        return [
            'sonata.admin.product' => [
                'image' => ['width' => self::S_800, 'height' => self::S_750],
            ],
            'sonata.admin.product_has_image' => [
                'image' => ['width' => self::S_800, 'height' => self::S_750],
            ],
            'sonata.admin.product_has_option_colour' => [
                'image' => ['width' => self::S_800, 'height' => self::S_750],
            ],
        ];
    }
}