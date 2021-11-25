<?php

namespace ProductBundle\Helper;

use Doctrine\ORM\EntityManager;
use ProductBundle\Entity\Product;
use ProductBundle\Entity\ProductInfoView;

/**
 * Class ProductViewHelper
 */
class ProductViewHelper
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * ArticleExtension constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Product $product
     *
     * @return bool
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function doView(Product $product)
    {
        $now = new \DateTime('now');
        $viewProduct = $this->getProductInfoView($product);

        if ($viewProduct) {
            $viewProduct->doView();

            $this->entityManager->persist($viewProduct);
            $this->entityManager->flush();
        } else {
            $viewProduct = new ProductInfoView();
            $viewProduct->doView();
            $viewProduct->setProduct($product);
            $viewProduct->setViewAt($now);

            $this->entityManager->persist($viewProduct);
            $this->entityManager->flush();
        }

        return true;
    }

    /**
     * @param Product $product
     *
     * @return bool
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function doDownload(Product $product)
    {
        $viewBook = $this->getProductInfoView($product);

        if ($viewBook) {
            $viewBook->doDownload();

            $this->entityManager->persist($viewBook);
            $this->entityManager->flush();
        }

        return true;
    }

    /**
     * @param Product $book
     *
     * @return ProductInfoView|object|null
     *
     * @throws \Exception
     */
    protected function getProductInfoView(Product $product)
    {
        $repository = $this->entityManager->getRepository(ProductInfoView::class);

        $now = new \DateTime('now');
        return $repository->findOneBy(['product' => $product, 'viewAt' => $now]);
    }
}
