<?php

namespace AppBundle\Services;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use MediaBundle\Entity\MediaImage;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;

class ImageOptimizer
{
    use ContainerAwareTrait;

    private const MAX_WIDTH = 500;
    private const MAX_HEIGHT = 500;

    /**
     * @var Imagine
     */
    private $imagine;

    /**
     * @var RequestStack
     */
    private $request;

    /**
     *
     */
    public function __construct()
    {
        $this->imagine = new Imagine();
    }

    /**
     * @param  RequestStack  $request
     *
     * @return void
     */
    public function setRequest(RequestStack $request)
    {
        $this->request = $request;
    }

    /**
     * @param  MediaImage  $object
     *
     * @return void
     */
    public function resize(MediaImage $object): void
    {
        $request = $this->request->getCurrentRequest();
        if ($request->get('pcode') && $request->get('field_name')) {
            $sizes = $this->getSizes();

            $path = $this->container->getParameter('imagine_web_root'). $object->getPath();

            if (array_key_exists($request->get('pcode'), $sizes)) {
                $adminCodes = $sizes[$request->get('pcode')];
                if (array_key_exists($request->get('field_name'), $adminCodes)) {
                    [$iwidth, $iheight] = getimagesize($path);
                    $ratio = $iwidth / $iheight;
                    $width = $adminCodes[$request->get('field_name')]['width'];
                    $height = $adminCodes[$request->get('field_name')]['height'];

                    if ($width / $height > $ratio) {
                        $width = $height * $ratio;
                    } else {
                        $height = $width / $ratio;
                    }

                    $photo = $this->imagine->open($path);
                    $photo->resize(new Box($width, $height))->save($path, ['jpeg_quality' => 90]);

                    $file = $object->getFile();
                    [$width, $height] = getimagesize($path);
                    if ($width && $height) {
                        $object->setWidth($width);
                        $object->setHeight($height);
                    } else {
                        $object->setWidth(null);
                        $object->setHeight(null);
                    }
                    $object->setSize($file->getSize());

                    $em = $this->container->get('doctrine.orm.entity_manager');
                    $em->persist($object);
                    $em->flush();
                }
            }
        }
    }

    /**
     * @return \int[][]
     */
    protected function getSizes(): array
    {
        return [
            'sonata.admin.product' => [
                'image' => [
                    'width' => 500,
                    'height' => 500,
                ],

            ],
            'sonata.admin.product_has_image' => [
                'image' => [
                    'width' => 500,
                    'height' => 500,
                ],
            ],
            'sonata.admin.product_has_option_colour' => [
                'image' => [
                    'width' => 500,
                    'height' => 500,
                ],
            ]
        ];
    }
}