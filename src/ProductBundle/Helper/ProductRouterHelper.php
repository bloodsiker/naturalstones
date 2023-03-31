<?php

namespace ProductBundle\Helper;

use ProductBundle\Entity\Category;
use ProductBundle\Entity\Product;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class ProductRouterHelper
 */
class ProductRouterHelper
{
    const PRODUCT_ROUTE = 'product_view';
    const CATEGORY_ROUTE = 'product_list';

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
     * @return string|null
     */
    public function getProductPath(Product $product, $needAbsolute = false, $domain = null)
    {
        $path = null;
        if ($product->getCategory()) {
            $path = $this->router->generate(
                self::PRODUCT_ROUTE,
                [
                    'category' => $product->getCategory()->getSlug(),
                    'id' => $product->getId(),
                    'slug' => $product->getSlug(),
                ],
                $needAbsolute ? UrlGeneratorInterface::ABSOLUTE_URL : UrlGeneratorInterface::ABSOLUTE_PATH,
                $domain
            );
        }

        return $path;
    }

    public function getCategoryPath(Category $category, $needAbsolute = false, $domain = null)
    {
        $path = null;
        if ($category->getSlug()) {
            $path = $this->router->generate(
                self::CATEGORY_ROUTE,
                [
                    'slug' => $category->getSlug(),
                ],
                $needAbsolute ? UrlGeneratorInterface::ABSOLUTE_URL : UrlGeneratorInterface::ABSOLUTE_PATH,
                $domain
            );
        }

        return $path;
    }
}
