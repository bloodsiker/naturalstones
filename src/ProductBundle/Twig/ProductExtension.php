<?php

namespace ProductBundle\Twig;

use ProductBundle\Entity\Product;
use ProductBundle\Helper\ProductRouterHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Class ProductExtension
 */
class ProductExtension extends AbstractExtension
{
    private ProductRouterHelper $productRouterHelper;

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
            new TwigFunction('product_path', [$this, 'getProductPath']),
        ];
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new TwigFilter('video_url', [$this, 'getVideoUrl']),
        );
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

    /**
     * @param $path
     *
     * @return string
     */
    public function getVideoUrl($path)
    {
        $queryString = parse_url($path, PHP_URL_QUERY);
        parse_str($queryString, $queryParams);
        $videoId = $queryParams['v'];

        return sprintf('https://www.youtube.com/embed/%s?feature=oembed', $videoId);
    }
}
