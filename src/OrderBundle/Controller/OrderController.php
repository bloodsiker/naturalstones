<?php

namespace OrderBundle\Controller;

use OrderBundle\Entity\Order;
use OrderBundle\Entity\OrderHasItem;
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
        $messenger = $request->get('messenger');
        if (!$phone) {
            return new JsonResponse([
                'type' => 'error',
                'message' => 'Не указан номер телефона'
            ]);
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $router = $this->get('router');
        $encrypt = $this->get('app.helper.encrypt');
        $cartService = $this->get('app.cart');
        $cart = $cartService->getProductsInfo();

        if (!isset($cart['product']) || !count($cart['product'])) {
            return new JsonResponse([
                'type' => 'error',
                'message' => 'В корзине нет товаров!'
            ]);
        }

        $totalPrice = 0;

        $order = new Order();
        $order->setPhone($phone);
        $order->setMessenger($messenger);
        $order->setType(Order::TYPE_ORDER_QUICK);
        foreach ($cart['product'] as $item) {
            $totalPrice += $item['totalPrice'];
            $product = $item['item'];
            $orderHasItem = new OrderHasItem();
            $orderHasItem->setProduct($product);
            $orderHasItem->setDiscount($product->getDiscount());
            $orderHasItem->setPrice($product->getPrice());
            $orderHasItem->setQuantity($item['count']);
            $order->addOrderHasItem($orderHasItem);
            $em->persist($orderHasItem);
        }
        $order->setTotalSum($totalPrice);
        $em->persist($order);
        $em->flush();

        $cartService->clear();

        return new JsonResponse([
            'type' => 'success',
            'url' => $router->generate('success_order', ['hash' => $encrypt->stringEncrypt($order->getId())], Router::ABSOLUTE_URL)
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function orderAction(Request $request)
    {
        $orderId = $request->get('orderId');
        if ($orderId) {
            $em = $this->get('doctrine.orm.entity_manager');
            $repository = $em->getRepository(OrderBoard::class);
            $order = $repository->find((int) $orderId);
            if ($order) {
                if ($request->isXmlHttpRequest() && $request->getMethod() === 'POST') {
                    $ip = ip2long($request->server->get('REMOTE_ADDR'));
                    $resultVoted = $em->getRepository(OrderBoardVotesResult::class)
                        ->findOneBy(['ip' => $ip, 'orderBoard' => $order]);
                    if (!$resultVoted) {
                        $order->increaseVote();
                        $em->persist($order);

                        $resultVoted = new OrderBoardVotesResult();
                        $resultVoted->setOrderBoard($order);
                        $resultVoted->setIp($ip);
                        $em->persist($resultVoted);
                        $em->flush();

                        return new JsonResponse([
                            'count' => $order->getVote(),
                            'message' => 'Вы поддержали книгу',
                            'type' => 'success',
                        ]);
                    } else {
                        return new JsonResponse([
                            'count' => $order->getVote(),
                            'message' => 'Ранее, Вы уже отдавали голос за эту книгу',
                            'type' => 'error',
                        ]);
                    }
                }
            }
        }

        return new JsonResponse();
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

//        dump($order);die;
        return $this->render('OrderBundle::order_success.html.twig', ['order' => $order]);
    }
}