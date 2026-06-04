<?php

namespace OrderBundle\Controller;

use AppBundle\Services\Cart;
use AppBundle\Services\OrderEmailService;
use AppBundle\Services\SendTelegramService;
use Doctrine\ORM\EntityManagerInterface;
use OrderBundle\Entity\Order;
use AppBundle\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class OrderController
 */
class OrderController extends BaseController
{
    const ORDER_404 = 'Order doesn\'t exist';

    private RouterInterface $router;
    private Cart $cartService;
    private SendTelegramService $telegramService;
    private OrderEmailService $orderEmailService;
    private EntityManagerInterface $entityManager;

    public function __construct(
        RouterInterface $router,
        Cart $cartService,
        SendTelegramService $telegramService,
        OrderEmailService $orderEmailService,
        EntityManagerInterface $entityManager
    ) {
        $this->router = $router;
        $this->cartService = $cartService;
        $this->telegramService = $telegramService;
        $this->orderEmailService = $orderEmailService;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     */
     #[Cache(maxage: 60, public: true)]
    public function quickOrderAction(Request $request)
    {
        $phone = $request->get('phone');
        if (in_array($request->get('messenger'), ['telegram', 'viber']) && !$phone) {
            return new JsonResponse([
                'type' => 'error',
                'message' => 'Не указан номер телефона'
            ]);
        }

        if ($request->get('messenger') === 'instagram' && !$request->get('instagram')) {
            return new JsonResponse([
                'type' => 'error',
                'message' => 'Не указана ссылка на инстаграм'
            ]);
        }

        $cart = $this->cartService->getProductsInfo();

        if (!isset($cart['product']) || !count($cart['product'])) {
            return new JsonResponse([
                'type' => 'error',
                'message' => 'В корзине нет товаров!'
            ]);
        }

        $order = $this->cartService->orderCart($request);
        $this->telegramService->sendMessageFromQuickForm($order);
        $this->orderEmailService->sendOrderCreatedEmail($order);

        return new JsonResponse([
            'type' => 'success',
            'url' => $this->router->generate('success_order', ['secret' => $order->getSecret()], RouterInterface::ABSOLUTE_URL)
        ]);
    }

    public function productQuickOrderAction(Request $request)
    {
        if (in_array($request->get('messenger'), ['telegram', 'viber']) && !$request->get('phone')) {
            return new JsonResponse([
                'type' => 'error',
                'message' => 'Не указан номер телефона'
            ]);
        }

        if ($request->get('messenger') === 'instagram' && !$request->get('instagram')) {
            return new JsonResponse([
                'type' => 'error',
                'message' => 'Не указана ссылка на инстаграм'
            ]);
        }

        if (!$request->get('product')) {
            return new JsonResponse([
                'type' => 'error',
                'message' => 'Не выбран товар для заказа'
            ]);
        }

        $order = $this->cartService->orderCart($request);
        $this->telegramService->sendMessageFromQuickForm($order);
        $this->orderEmailService->sendOrderCreatedEmail($order);

        return new JsonResponse([
            'type' => 'success',
            'url' => $this->router->generate('success_order', ['secret' => $order->getSecret()], RouterInterface::ABSOLUTE_URL)
        ]);
    }

    public function orderAction(Request $request)
    {
        $phone = $request->get('phone');
        if (in_array($request->get('messenger'), ['telegram', 'viber']) && !$phone) {
            return new JsonResponse([
                'type' => 'error',
                'message' => 'Не указан номер телефона'
            ]);
        }

        if ($request->get('messenger') === 'instagram' && !$request->get('instagram')) {
            return new JsonResponse([
                'type' => 'error',
                'message' => 'Не указана ссылка на инстаграм'
            ]);
        }

        $cart = $this->cartService->getProductsInfo();
        if (!isset($cart['product']) || !count($cart['product'])) {
            return new JsonResponse([
                'type' => 'error',
                'message' => 'В корзине нет товаров!'
            ]);
        }

        $order = $this->cartService->orderCart($request, Order::TYPE_ORDER_CART);
        $this->telegramService->sendMessageFromCart($order);
        $this->orderEmailService->sendOrderCreatedEmail($order);

        return new JsonResponse([
            'type' => 'success',
            'url' => $this->router->generate('success_order', ['secret' => $order->getSecret()], RouterInterface::ABSOLUTE_URL)
        ]);
    }

    public function successAction(Request $request)
    {
        $secret = $request->get('secret');
//        $idOrder = $this->get('app.helper.encrypt')->stringDecrypt($hash);
        $repo = $this->entityManager->getRepository(Order::class);
        $order = $repo->findOneBy(['secret' => $secret]);
        if (!$order) {
            throw $this->createNotFoundException(self::ORDER_404);
        }

        return $this->render('@Order/order_success.html.twig', ['order' => $order]);
    }
}
