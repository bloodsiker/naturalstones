<?php

namespace ProductBundle\Twig;

use GenreBundle\Entity\Genre;
use GenreBundle\Helper\GenreRouterHelper;
use ProductBundle\Entity\Product;
use ProductBundle\Helper\ProductRouterHelper;

/**
 * Class ProductExtension
 */
class ProductExtension extends \Twig_Extension
{
    /**
     * @var ProductRouterHelper
     */
    private $productRouterHelper;

    /**
     * ArticleExtension constructor.
     *
     * @param ProductRouterHelper $productRouterHelper
     */
    public function __construct(ProductRouterHelper $productRouterHelper)
    {
        $this->productRouterHelper = $productRouterHelper;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'product_extension';
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('product_path', [$this, 'getProductPath']),
        ];
    }

    /**
     * @param  Product  $product
     * @param  false  $needAbsolute
     *
     * @return string
     */
    public function getProductPath(Product $product, $needAbsolute = false)
    {
        return $this->productRouterHelper->getProductPath($product, $needAbsolute);
    }
}
