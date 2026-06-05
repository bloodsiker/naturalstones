<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use OrderBundle\Entity\Order;
use OrderBundle\Entity\OrderHasItem;
use ProductBundle\Entity\Product;
use ShareBundle\Entity\Colour;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use UserBundle\Entity\User;

class Cart
{
    public const SESSION_CART = 'user_cart';
    public const TYPE_PRODUCT = 'product';

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly EntityManagerInterface $entityManager,
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    /**
     * @return array<string, array<string, array<string, mixed>>>|null
     */
    public function getProductInCart(): ?array
    {
        return $this->session()->get(self::SESSION_CART);
    }

    /**
     * @param array{option: string, value: string|int}|array{} $options
     */
    public function addProductToCart(
        string $type,
        string $id,
        int $count,
        ?int $colour = null,
        array $options = [],
    ): int {
        $productsInCart = $this->getProductInCart() ?: [];

        $count = max($count, 1);
        $key = $id;

        if ($colour) {
            $key .= sprintf(':colour:%s', $colour);
        }

        if ($options) {
            $key .= sprintf(':%s:%s', $options['option'], $options['value']);
        }

        $existing = $productsInCart[$type][$key] ?? null;
        $productsInCart[$type][$key]['id'] = $id;
        $productsInCart[$type][$key]['count'] = ($existing['count'] ?? 0) + $count;

        if ($colour) {
            $productsInCart[$type][$key]['colour'] = $colour;
        }
        if ($options) {
            $productsInCart[$type][$key][$options['option']] = $options['value'];
        }

        $this->setCart($productsInCart);

        return $this->countItems();
    }

    /**
     * @return array<string, array<string, array<string, mixed>>>
     */
    public function getProductsInfo(): array
    {
        $productsInCart = $this->getProductInCart();
        $productRepository = $this->entityManager->getRepository(Product::class);
        $colourRepository = $this->entityManager->getRepository(Colour::class);

        $productsInfo = [];

        if (!is_array($productsInCart) || !array_key_exists(self::TYPE_PRODUCT, $productsInCart)) {
            return $productsInfo;
        }

        foreach ($productsInCart[self::TYPE_PRODUCT] as $key => $data) {
            $infoProduct = $productRepository->find($data['id']);
            if (!$infoProduct) {
                continue;
            }

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

            foreach (['letter', 'insert', 'pendant', 'bracelet', 'ring', 'necklace', 'earring', 'money'] as $optionKey) {
                if (isset($data[$optionKey])) {
                    $productsInfo[self::TYPE_PRODUCT][$key][$optionKey] = $data[$optionKey];
                }
            }
        }

        return $productsInfo;
    }

    public function recalculateCart(string $type, string $key, int $count): bool
    {
        $productsInCart = $this->getProductInCart() ?: [];
        $productsInCart[$type][$key]['count'] = max($count, 1);

        $this->setCart($productsInCart);

        return true;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function orderCart(Request $request, int $orderType = Order::TYPE_ORDER_QUICK): Order
    {
        $option = $request->get('option');
        $optionValue = $request->get('option_value');

        $productObject = null;
        if ($request->get('product')) {
            $productObject = $this->entityManager->getRepository(Product::class)->find($request->get('product'));
        }

        $colourObject = null;
        if ($colour = $request->get('colour_id')) {
            $colourObject = $this->entityManager->getRepository(Colour::class)->find($colour);
        }

        $order = (new Order())
            ->setFio($request->get('name'))
            ->setEmail($request->get('email'))
            ->setPhone($request->get('phone'))
            ->setInstagram($request->get('instagram'))
            ->setAddress($request->get('address'))
            ->setComment($request->get('comment'))
            ->setMessenger($request->get('messenger'))
            ->setType($orderType)
            ->setCallMe((bool) $request->get('call_me'));

        $token = $this->tokenStorage->getToken();
        if ($token && ($user = $token->getUser()) instanceof User) {
            $order->setUser($user);
        }

        if ($productObject) {
            $productObject->setFinalPrice($colourObject);
            $products = [[
                'item' => $productObject,
                'totalPrice' => $productObject->getFinalPrice(),
                'count' => 1,
                'colour' => $colourObject,
                $option => $optionValue,
            ]];
        } else {
            $products = $this->getProductsInfo()['product'] ?? [];
        }

        $totalPrice = 0;
        foreach ($products as $item) {
            $optionText = $this->buildOptionText($item);
            $totalPrice += $item['totalPrice'];

            /** @var Product $product */
            $product = $item['item'];
            $orderHasItem = (new OrderHasItem())
                ->setProduct($product)
                ->setColour($item['colour'] ?? null)
                ->setDiscount($product->getDiscount())
                ->setPrice($item['totalPrice'])
                ->setQuantity($item['count'])
                ->setOptions($optionText);

            $order->addOrderHasItem($orderHasItem);
            $this->entityManager->persist($orderHasItem);
        }

        $order->setOrderSum($totalPrice);
        $order->setTotalSum($totalPrice);
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $this->clear();
        $this->session()->remove('infoCart');

        return $order;
    }

    public function countItems(): int
    {
        $productsInCart = $this->getProductInCart();
        if (!is_array($productsInCart) || !array_key_exists(self::TYPE_PRODUCT, $productsInCart)) {
            return 0;
        }

        $count = 0;
        foreach ($productsInCart[self::TYPE_PRODUCT] as $data) {
            $count += $data['count'];
        }

        return $count;
    }

    public function getTotalPrice(): float
    {
        $productsInCart = $this->getProductsInfo();
        if (!array_key_exists(self::TYPE_PRODUCT, $productsInCart)) {
            return 0.0;
        }

        $totalPrice = 0.0;
        foreach ($productsInCart[self::TYPE_PRODUCT] as $data) {
            $totalPrice += $data['totalPrice'];
        }

        return $totalPrice;
    }

    public function deleteProduct(string $type, string $key): int
    {
        $productsInCart = $this->getProductInCart();
        unset($productsInCart[$type][$key]);
        $this->setCart($productsInCart);

        return $this->countItems();
    }

    public function clear(): bool
    {
        if ($this->session()->has(self::SESSION_CART)) {
            $this->session()->remove(self::SESSION_CART);
        }

        return true;
    }

    private function session(): SessionInterface
    {
        return $this->requestStack->getSession();
    }

    /**
     * @param array<string, mixed> $products
     */
    private function setCart(array $products): void
    {
        $this->session()->set(self::SESSION_CART, $products);
    }

    /**
     * @param array<string, mixed> $item
     */
    private function buildOptionText(array $item): ?string
    {
        $labels = [
            'letter' => 'Буква',
            'insert' => 'Вставка',
            'pendant' => 'Подвеска',
            'bracelet' => 'Браслет',
            'ring' => 'Кольцо',
            'necklace' => 'Колье',
            'earring' => 'Серьги',
            'money' => 'Сума',
        ];

        $lines = [];
        foreach ($labels as $key => $label) {
            if (!empty($item[$key])) {
                $lines[] = $label . ': ' . $item[$key];
            }
        }

        return $lines ? implode(PHP_EOL, $lines) . PHP_EOL : null;
    }
}