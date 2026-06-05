<?php

namespace ProductBundle\Helper;

use ProductBundle\Entity\Category;
use ProductBundle\Entity\Product;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class ProductRouterHelper
{
    public const PRODUCT_ROUTE = 'product_view';
    public const CATEGORY_ROUTE = 'product_list';

    public function __construct(
        private readonly RouterInterface $router,
    ) {
    }

    public function getProductPath(Product $product, bool $needAbsolute = false, ?string $domain = null): ?string
    {
        if (!$product->getCategory()) {
            return null;
        }

        return $this->router->generate(
            self::PRODUCT_ROUTE,
            [
                'category' => $product->getCategory()->getSlug(),
                'id' => $product->getId(),
                'slug' => $product->getSlug(),
            ],
            $this->referenceType($needAbsolute),
            $domain
        );
    }

    public function getCategoryPath(Category $category, bool $needAbsolute = false, ?string $domain = null): ?string
    {
        if (!$category->getSlug()) {
            return null;
        }

        return $this->router->generate(
            self::CATEGORY_ROUTE,
            ['slug' => $category->getSlug()],
            $this->referenceType($needAbsolute),
            $domain
        );
    }

    private function referenceType(bool $needAbsolute): int
    {
        return $needAbsolute ? UrlGeneratorInterface::ABSOLUTE_URL : UrlGeneratorInterface::ABSOLUTE_PATH;
    }
}