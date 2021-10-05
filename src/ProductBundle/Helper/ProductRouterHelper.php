<?php

namespace ProductBundle\Helper;

use ProductBundle\Entity\Product;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class ProductRouterHelper
 */
class ProductRouterHelper
{
    const PRODUCT_ROUTE = 'product_view';

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * ArticleExtension constructor.
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param  Product  $product
     * @param  false  $needAbsolute
     *
     * @return string
     */
    public function getGenrePath(Product $product, $needAbsolute = false) : string
    {
        $path = $this->router->generate(
            self::PRODUCT_ROUTE,
            [
                'category' => $product->getCategory()->getSlug(),
                'id' => $product->getId(),
                'slug' => $product->getSlug(),
            ],
            $needAbsolute ? UrlGeneratorInterface::ABSOLUTE_URL : UrlGeneratorInterface::ABSOLUTE_PATH
        );

        return $path;
    }
}
