<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use OrderBundle\Entity\Order;
use OrderBundle\Entity\OrderHasItem;
use ProductBundle\Entity\Product;
use ShareBundle\Entity\Colour;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class Cart
 */
class Cart
{
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
     * @param  string  $type
     * @param  string  $id
     * @param  int     $count
     * @param  int|null  $colour
     *
     * @return mixed
     */
    public function addProductToCart(string $type, string $id, int $count, $colour = null, $options = [])
    {
        $productsInCart = $this->getProductInCart() ?: [];

        $count = max($count, 1);

        $key = $id;
        if ($colour) {
            $key .= sprintf(':colour:%s', $colour);
        }

        if ($options) {
            $key .= sprintf(':%s:%s', $options['option'], $options['value']);
        }

        if (array_key_exists($type, $productsInCart)) {
            if (array_key_exists($key, $productsInCart[$type])) {
                $productsInCart[$type][$key]['id'] = $id;
                $productsInCart[$type][$key]['count'] += $count;
            } else {
                $productsInCart[$type][$key]['id'] = $id;
                $productsInCart[$type][$key]['count'] = $count;
            }
            if ($colour) {
                $productsInCart[$type][$key]['colour'] = $colour;
            }
            if ($options) {
                $productsInCart[$type][$key][$options['option']] = $options['value'];
            }
        } else {
            $productsInCart[$type][$key]['id'] = $id;
            $productsInCart[$type][$key]['count'] = $count;
            if ($colour) {
                $productsInCart[$type][$key]['colour'] = $colour;
            }
            if ($options) {
                $productsInCart[$type][$key][$options['option']] = $options['value'];
            }
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
        $productRepository = $this->entityManager->getRepository(Product::class);
        $colourRepository = $this->entityManager->getRepository(Colour::class);

        $productsInfo = [];

        if ($productsInCart && is_array($productsInCart)) {
            if (array_key_exists(self::TYPE_PRODUCT, $productsInCart)) {
                foreach ($productsInCart[self::TYPE_PRODUCT] as $key => $data) {
                    $infoProduct = $productRepository->find($data['id']);
                    if ($infoProduct) {
                        $infoProduct->setFinalPrice();
                        if (isset($data['colour'])) {
                            $infoColour = $colourRepository->find($data['colour']);
                            if ($infoColour) {
                                $productsInfo[self::TYPE_PRODUCT][$key]['colour'] = $infoColour;
                                $infoProduct->setFinalPrice($infoColour);
                            }
                        }
                        $productsInfo[self::TYPE_PRODUCT][$key]['item'] = $infoProduct;
                        $productsInfo[self::TYPE_PRODUCT][$key]['count'] = $data['count'];
                        $productsInfo[self::TYPE_PRODUCT][$key]['price'] = $infoProduct->getFinalPrice();
                        $productsInfo[self::TYPE_PRODUCT][$key]['totalPrice'] = $data['count'] * $infoProduct->getFinalPrice();

                        if (isset($data['letter'])) {
                            $productsInfo[self::TYPE_PRODUCT][$key]['letter'] = $data['letter'];
                        }
                        if (isset($data['insert'])) {
                            $productsInfo[self::TYPE_PRODUCT][$key]['insert'] = $data['insert'];
                        }
                        if (isset($data['pendant'])) {
                            $productsInfo[self::TYPE_PRODUCT][$key]['pendant'] = $data['pendant'];
                        }
                        if (isset($data['bracelet'])) {
                            $productsInfo[self::TYPE_PRODUCT][$key]['bracelet'] = $data['bracelet'];
                        }
                        if (isset($data['ring'])) {
                            $productsInfo[self::TYPE_PRODUCT][$key]['ring'] = $data['ring'];
                        }
                        if (isset($data['necklace'])) {
                            $productsInfo[self::TYPE_PRODUCT][$key]['necklace'] = $data['necklace'];
                        }
                        if (isset($data['earring'])) {
                            $productsInfo[self::TYPE_PRODUCT][$key]['earring'] = $data['earring'];
                        }
                        if (isset($data['money'])) {
                            $productsInfo[self::TYPE_PRODUCT][$key]['money'] = $data['money'];
                        }
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
     * @param string $key
     * @param int    $count
     *
     * @return bool
     */
    public function recalculateCart($type, string $key, int $count)
    {
        $productsInCart = $this->getProductInCart() ?: [];

        $count = $count < 1 ? 1 : $count;

        if (array_key_exists($type, $productsInCart)) {
            if (array_key_exists($key, $productsInCart[$type])) {
                $productsInCart[$type][$key]['count'] = $count;
            } else {
                $productsInCart[$type][$key]['count'] = $count;
            }
        } else {
            $productsInCart[$type][$key]['count'] = $count;
        }

        $this->setCart($productsInCart);

        return true;
    }

    /**
     * @param  Request  $request
     *
     * @return Order
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function orderCart(Request $request, $orderType = Order::TYPE_ORDER_QUICK)
    {
        $fio = $request->get('name');
        $email = $request->get('email');
        $phone = $request->get('phone');
        $instagram = $request->get('instagram');
        $address = $request->get('address');
        $comment = $request->get('comment');
        $messenger = $request->get('messenger');
        $colour = $request->get('colour_id');
        $option = $request->get('option');
        $optionValue = $request->get('option_value');
        $callMe = $request->get('call_me') ?? false;

        $productObject = null;
        if ($request->get('product')) {
            $productRepository = $this->entityManager->getRepository(Product::class);
            $productObject = $productRepository->find($request->get('product'));
        }
        $colourObject = null;
        if ($colour) {
            $colourRepository = $this->entityManager->getRepository(Colour::class);
            $colourObject = $colourRepository->find($colour);
        }

        $totalPrice = 0;

        $order = new Order();
        $order->setFio($fio);
        $order->setEmail($email);
        $order->setPhone($phone);
        $order->setInstagram($instagram);
        $order->setAddress($address);
        $order->setComment($comment);
        $order->setMessenger($messenger);
        $order->setType($orderType);
        $order->setCallMe($callMe);

        if ($productObject) {
            $productObject->setFinalPrice($colourObject);
            $products[0] = [
                'item' => $productObject,
                'totalPrice' => $productObject->getFinalPrice(),
                'count' => 1,
                'colour' => $colourObject,
                $option => $optionValue,
            ];
        } else {
            $products = $this->getProductsInfo()['product'];
        }

        foreach ($products as $item) {
            $option = null;
            if (isset($item['letter']) && $item['letter']) {
                $option .= 'Буква: ' . $item['letter'] . PHP_EOL;
            }
            if (isset($item['insert']) && $item['insert']) {
                $option .= 'Вставка: ' . $item['insert'] . PHP_EOL;
            }
            if (isset($item['pendant']) && $item['pendant']) {
                $option .= 'Подвеска: ' . $item['pendant'] . PHP_EOL;
            }
            if (isset($item['bracelet']) && $item['bracelet']) {
                $option .= 'Браслет: ' . $item['bracelet'] . PHP_EOL;
            }
            if (isset($item['ring']) && $item['ring']) {
                $option .= 'Кольцо: ' . $item['ring'] . PHP_EOL;
            }
            if (isset($item['necklace']) && $item['necklace']) {
                $option .= 'Колье: ' . $item['necklace'] . PHP_EOL;
            }
            if (isset($item['earring']) && $item['earring']) {
                $option .= 'Серьги: ' . $item['earring'] . PHP_EOL;
            }
            if (isset($item['money']) && $item['money']) {
                $option .= 'Сума: ' . $item['money'] . PHP_EOL;
            }
            $totalPrice += $item['totalPrice'];
            $product = $item['item'];
            $orderHasItem = new OrderHasItem();
            $orderHasItem->setProduct($product);
            $orderHasItem->setColour($item['colour'] ?? null);
            $orderHasItem->setDiscount($product->getDiscount());
            $orderHasItem->setPrice($item['totalPrice']);
            $orderHasItem->setQuantity($item['count']);
            $orderHasItem->setOptions($option);
            $order->addOrderHasItem($orderHasItem);
            $this->entityManager->persist($orderHasItem);
        }
        $order->setOrderSum($totalPrice);
        $order->setTotalSum($totalPrice);
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $this->clear();
        $this->session->remove('infoCart');

        return $order;
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
     * @param string $key
     *
     * @return bool
     */
    public function deleteProduct($type, string $key)
    {
        $productsInCart = $this->getProductInCart();

        unset($productsInCart[$type][$key]);

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