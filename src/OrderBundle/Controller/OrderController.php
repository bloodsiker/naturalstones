<?php

namespace OrderBundle\Controller;

use OrderBundle\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\Routing\Router;

/**
 * Class OrderController
 */
class OrderController extends Controller
{
    const ORDER_404 = 'Order doesn\'t exist';

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Cache(maxage=60, public=true)
     */
    public function quickOrderAction(Request $request)
    {
        $phone = $request->get('phone');
        if (!$phone) {
            return new JsonResponse([
                'type' => 'error',
                'message' => 'Не указан номер телефона'
            ]);
        }

        $router = $this->get('router');
        $encrypt = $this->get('app.helper.encrypt');
        $cartService = $this->get('app.cart');
        $telegramService = $this->get('app.send_telegram');
        $cart = $cartService->getProductsInfo();

        if (!isset($cart['product']) || !count($cart['product'])) {
            return new JsonResponse([
                'type' => 'error',
                'message' => 'В корзине нет товаров!'
            ]);
        }

        $order = $cartService->orderCart($request);
        $telegramService->sendMessageFromQuickForm($order);

        return new JsonResponse([
            'type' => 'success',
            'url' => $router->generate('success_order', ['hash' => $encrypt->stringEncrypt($order->getId())], Router::ABSOLUTE_URL)
        ]);
    }

    public function productQuickOrderAction(Request $request)
    {
        if (!$request->get('phone')) {
            return new JsonResponse([
                'type' => 'error',
                'message' => 'Не указан номер телефона'
            ]);
        }

        if (!$request->get('product')) {
            return new JsonResponse([
                'type' => 'error',
                'message' => 'Не выбран товар для заказа'
            ]);
        }

        $router = $this->get('router');
        $encrypt = $this->get('app.helper.encrypt');
        $cartService = $this->get('app.cart');
        $telegramService = $this->get('app.send_telegram');

        $order = $cartService->orderCart($request);
        $telegramService->sendMessageFromQuickForm($order);

        return new JsonResponse([
            'type' => 'success',
            'url' => $router->generate('success_order', ['hash' => $encrypt->stringEncrypt($order->getId())], Router::ABSOLUTE_URL)
        ]);
    }

    public function orderAction(Request $request)
    {
        $phone = $request->get('phone');
        if (!$phone) {
            return new JsonResponse([
                'type' => 'error',
                'message' => 'Не указан номер телефона'
            ]);
        }

        $router = $this->get('router');
        $encrypt = $this->get('app.helper.encrypt');
        $cartService = $this->get('app.cart');
        $telegramService = $this->get('app.send_telegram');
        $cart = $cartService->getProductsInfo();

        if (!isset($cart['product']) || !count($cart['product'])) {
            return new JsonResponse([
                'type' => 'error',
                'message' => 'В корзине нет товаров!'
            ]);
        }

        $order = $cartService->orderCart($request, Order::TYPE_ORDER_CART);
        $telegramService->sendMessageFromCart($order);

        return new JsonResponse([
            'type' => 'success',
            'url' => $router->generate('success_order', ['hash' => $encrypt->stringEncrypt($order->getId())], Router::ABSOLUTE_URL)
        ]);
    }

    public function successAction(Request $request)
    {
        $hash = $request->get('hash');
        $idOrder = $this->get('app.helper.encrypt')->stringDecrypt($hash);
        $em = $this->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository(Order::class);
        $order = $repo->find((int) $idOrder);
        if (!$order) {
            throw $this->createNotFoundException(self::ORDER_404);
        }

        return $this->render('OrderBundle::order_success.html.twig', ['order' => $order]);
    }
}