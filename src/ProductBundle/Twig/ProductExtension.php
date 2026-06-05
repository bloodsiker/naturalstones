<?php

namespace ProductBundle\Twig;

use ProductBundle\Entity\Product;
use ProductBundle\Helper\ProductRouterHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ProductExtension extends AbstractExtension
{
    public function __construct(
        private readonly ProductRouterHelper $productRouterHelper,
    ) {
    }

    public function getName(): string
    {
        return 'product_extension';
    }

    /**
     * @return list<TwigFunction>
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('product_path', $this->getProductPath(...)),
        ];
    }

    /**
     * @return list<TwigFilter>
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('video_url', $this->getVideoUrl(...)),
        ];
    }

    public function getProductPath(Product $product, bool $needAbsolute = false): ?string
    {
        return $this->productRouterHelper->getProductPath($product, $needAbsolute);
    }

    public function getVideoUrl(string $path): string
    {
        parse_str((string) parse_url($path, PHP_URL_QUERY), $queryParams);

        return sprintf('https://www.youtube.com/embed/%s?feature=oembed', $queryParams['v'] ?? '');
    }
}