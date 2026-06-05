<?php

namespace ProductBundle\Helper;

use Doctrine\ORM\EntityManagerInterface;
use ProductBundle\Entity\Product;
use ProductBundle\Entity\ProductInfoView;

class ProductViewHelper
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function doView(Product $product): bool
    {
        $viewProduct = $this->getProductInfoView($product);

        if (!$viewProduct) {
            $viewProduct = new ProductInfoView();
            $viewProduct->setProduct($product);
            $viewProduct->setViewAt(new \DateTime('now'));
        }

        $viewProduct->doView();

        $this->entityManager->persist($viewProduct);
        $this->entityManager->flush();

        return true;
    }

    public function doDownload(Product $product): bool
    {
        $view = $this->getProductInfoView($product);
        if (!$view) {
            return true;
        }

        $view->doDownload();

        $this->entityManager->persist($view);
        $this->entityManager->flush();

        return true;
    }

    protected function getProductInfoView(Product $product): ?ProductInfoView
    {
        return $this->entityManager->getRepository(ProductInfoView::class)
            ->findOneBy(['product' => $product, 'viewAt' => new \DateTime('now')]);
    }
}