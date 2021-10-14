<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use ProductBundle\Entity\Product;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class Cart
 */
class Cart
{
    /**
     * Const
     */
    const SESSION_CART  = 'user_cart';
    const TYPE_PRODUCT  = 'product';

    /**
     * @var Session
     */
    private $session;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Cart constructor.
     * @param Session       $session
     * @param EntityManager $entityManager
     */
    public function __construct(Session $session, EntityManager $entityManager)
    {
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    /**
     * Get product in cart
     *
     * @return mixed
     */
    public function getProductInCart()
    {
        return $this->session->get(self::SESSION_CART);
    }

    /**
     * Add product to cart
     *
     * @param string $type
     * @param int    $id
     * @param int    $count
     *
     * @return mixed
     */
    public function addProductToCart($type, int $id, int $count)
    {
        $productsInCart = $this->getProductInCart() ?: [];

        $count = $count < 1 ? 1 : $count;

        if (array_key_exists($type, $productsInCart)) {
            if (array_key_exists($id, $productsInCart[$type])) {
                $productsInCart[$type][$id]['count'] += $count;
            } else {
                $productsInCart[$type][$id]['count'] = $count;
            }
        } else {
            $productsInCart[$type][$id]['count'] = $count;
        }

        $this->setCart($productsInCart);

        return $this->countItems();
    }

    /**
     * Get product info from cart
     *
     * @return array
     */
    public function getProductsInfo()
    {
        $productsInCart = $this->getProductInCart();
//        dump($productsInCart);die;
        $productRepository = $this->entityManager->getRepository(Product::class);

        $productsInfo = [];

        if ($productsInCart && is_array($productsInCart)) {
            if (array_key_exists(self::TYPE_PRODUCT, $productsInCart)) {
                foreach ($productsInCart[self::TYPE_PRODUCT] as $id => $data) {
                    $infoProduct = $productRepository->find($id);
                    if ($infoProduct) {
                        $productsInfo[self::TYPE_PRODUCT][$id]['item'] = $infoProduct;
                        $productsInfo[self::TYPE_PRODUCT][$id]['count'] = $data['count'];
                        $productsInfo[self::TYPE_PRODUCT][$id]['totalPrice'] = $data['count'] * ($infoProduct->getDiscount() ?: $infoProduct->getPrice());
                    }
                }
            }
        }

        return $productsInfo;
    }

    /**
     * Recalculate product in cart
     *
     * @param string $type
     * @param int    $id
     * @param int    $count
     *
     * @return bool
     */
    public function recalculateCart($type, int $id, int $count)
    {
        $productsInCart = $this->getProductInCart() ?: [];

        $count = $count < 1 ? 1 : $count;

        if (array_key_exists($type, $productsInCart)) {
            if (array_key_exists($id, $productsInCart[$type])) {
                $productsInCart[$type][$id]['count'] = $count;
            } else {
                $productsInCart[$type][$id]['count'] = $count;
            }
        } else {
            $productsInCart[$type][$id]['count'] = $count;
        }

        $this->setCart($productsInCart);

        return true;
    }

    /**
     * Get count product in cart
     *
     * @return int|mixed
     */
    public function countItems()
    {
        $productsInCart = $this->getProductInCart();
        $count = 0;

        if ($productsInCart && is_array($productsInCart)) {
            if (array_key_exists(self::TYPE_PRODUCT, $productsInCart)) {
                foreach ($productsInCart[self::TYPE_PRODUCT] as $data) {
                    $count += $data['count'];
                }
            }
        }

        return $count;
    }

    /**
     * Get total price in Cart
     *
     * @return int
     */
    public function getTotalPrice()
    {
        $productsInCart = $this->getProductsInfo();
        $totalPrice = 0;

        if ($productsInCart && is_array($productsInCart)) {
            if (array_key_exists(self::TYPE_PRODUCT, $productsInCart)) {
                foreach ($productsInCart[self::TYPE_PRODUCT] as $data) {
                    $totalPrice += $data['totalPrice'];
                }
            }
        }

        return $totalPrice;
    }

    /**
     * Remove product in cart
     *
     * @param string $type
     * @param int    $id
     *
     * @return bool
     */
    public function deleteProduct($type, int $id)
    {
        $productsInCart = $this->getProductInCart();

        unset($productsInCart[$type][$id]);

        $this->setCart($productsInCart);

        return $this->countItems();
    }

    /**
     * Clear all product in cart
     */
    public function clear()
    {
        if ($this->session->has(self::SESSION_CART)) {
            $this->session->remove(self::SESSION_CART);
        }

        return true;
    }

    /**
     * Set product in session
     *
     * @param $products
     */
    private function setCart($products)
    {
        $this->session->set(self::SESSION_CART, $products);
    }
}