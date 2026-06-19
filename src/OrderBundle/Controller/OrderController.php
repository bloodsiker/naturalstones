<?php

namespace OrderBundle\Controller;

use AppBundle\Controller\BaseController;
use AppBundle\Services\Cart;
use AppBundle\Services\OrderEmailService;
use AppBundle\Services\SendTelegramService;
use Doctrine\ORM\EntityManagerInterface;
use OrderBundle\Entity\Order;
use OrderBundle\Entity\PromoCode;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\RouterInterface;

class OrderController extends BaseController
{
    public const ORDER_404 = 'Order doesn\'t exist';

    private const MESSENGERS_REQUIRING_PHONE = ['telegram', 'viber'];

    public function __construct(
        private readonly RouterInterface $router,
        private readonly Cart $cartService,
        private readonly SendTelegramService $telegramService,
        private readonly OrderEmailService $orderEmailService,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Cache(maxage: 60, public: true)]
    public function quickOrderAction(Request $request): JsonResponse
    {
        if ($error = $this->validateMessenger($request)) {
            return $error;
        }
        if ($error = $this->validateCartHasProducts()) {
            return $error;
        }

        return $this->createOrderAndRespond($request, Order::TYPE_ORDER_QUICK, useCartMessage: false);
    }

    public function productQuickOrderAction(Request $request): JsonResponse
    {
        if ($error = $this->validateMessenger($request)) {
            return $error;
        }
        if (!$request->get('product')) {
            return $this->errorResponse('Не выбран товар для заказа');
        }

        return $this->createOrderAndRespond($request, Order::TYPE_ORDER_QUICK, useCartMessage: false);
    }

    public function orderAction(Request $request): JsonResponse
    {
        if ($error = $this->validateMessenger($request)) {
            return $error;
        }
        if ($error = $this->validateCartHasProducts()) {
            return $error;
        }

        return $this->createOrderAndRespond($request, Order::TYPE_ORDER_CART, useCartMessage: true);
    }

    public function applyPromoAction(Request $request): JsonResponse
    {
        $code = trim((string) $request->get('code'));
        $session = $request->getSession();

        if ($code === '') {
            $session->remove('promo_code_id');
            return new JsonResponse(['type' => 'removed']);
        }

        /** @var PromoCode|null $promo */
        $promo = $this->entityManager->getRepository(PromoCode::class)->findValidByCode($code);

        if (!$promo) {
            return new JsonResponse(['type' => 'error', 'message' => 'Промокод недійсний або вже не активний']);
        }

        $session->set('promo_code_id', $promo->getId());

        $total = $this->cartService->getTotalPrice();
        $discount = $promo->calculateDiscount($total);

        return new JsonResponse([
            'type'          => 'success',
            'code'          => $promo->getCode(),
            'discount_type' => $promo->getType(),
            'discount_value' => (float) $promo->getValue(),
            'discount'      => $discount,
            'total_after'   => max(0, $total - $discount),
        ]);
    }

    public function successAction(Request $request): Response
    {
        $order = $this->entityManager->getRepository(Order::class)
            ->findOneBy(['secret' => $request->get('secret')]);

        if (!$order) {
            throw $this->createNotFoundException(self::ORDER_404);
        }

        return $this->render('@Order/order_success.html.twig', ['order' => $order]);
    }

    private function createOrderAndRespond(Request $request, int $orderType, bool $useCartMessage): JsonResponse
    {
        $order = $this->cartService->orderCart($request, $orderType);

        if ($useCartMessage) {
            $this->telegramService->sendMessageFromCart($order);
        } else {
            $this->telegramService->sendMessageFromQuickForm($order);
        }

        $this->orderEmailService->sendOrderCreatedEmail($order);

        return new JsonResponse([
            'type' => 'success',
            'url' => $this->router->generate('success_order', ['secret' => $order->getSecret()], RouterInterface::ABSOLUTE_URL),
        ]);
    }

    private function validateMessenger(Request $request): ?JsonResponse
    {
        $messenger = $request->get('messenger');

        if (in_array($messenger, self::MESSENGERS_REQUIRING_PHONE, true) && !$request->get('phone')) {
            return $this->errorResponse('Не указан номер телефона');
        }

        if ('instagram' === $messenger && !$request->get('instagram')) {
            return $this->errorResponse('Не указана ссылка на инстаграм');
        }

        return null;
    }

    private function validateCartHasProducts(): ?JsonResponse
    {
        $cart = $this->cartService->getProductsInfo();
        if (empty($cart['product'])) {
            return $this->errorResponse('В корзине нет товаров!');
        }

        return null;
    }

    private function errorResponse(string $message): JsonResponse
    {
        return new JsonResponse(['type' => 'error', 'message' => $message]);
    }
}