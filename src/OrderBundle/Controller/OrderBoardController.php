<?php

namespace OrderBundle\Controller;

use OrderBundle\Entity\OrderBoard;
use OrderBundle\Entity\OrderBoardVotesResult;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

/**
 * Class OrderBoardController
 */
class OrderBoardController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Cache(maxage=60, public=true)
     */
    public function listAction(Request $request)
    {
        $router = $this->get('router');
        $breadcrumb = $this->get('app.breadcrumb');
        if (!$request->get('status')) {
            $breadcrumb->addBreadcrumb(['title' => 'Стол заказов']);
        } else {
            $breadcrumb->addBreadcrumb(['title' => 'Стол заказов', 'href' => $router->generate('order_board')]);
            $breadcrumb->addBreadcrumb(['title' => $this->get('translator')->trans(OrderBoard::getNameStatus($request->get('status')), [], 'OrderBundle')]);
        }
        $page = $request->get('page') ? " | Страница {$request->get('page', 1)}" : null;
        $pageDesc = $request->get('page') ? "Страница {$request->get('page', 1)} |" : null;

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => 'Стол заказов | TopBook.com.ua - скачать книги без регистрации в fb2, epub, pdf, txt форматах'.$page,
            'description' => $pageDesc.'Если на сайте нет книги, которую Вы хотите прочитать - нажмите кнопку "Добавить заказ" и мы постараемся ее добавить | ТопБук - электронная библиотека. Здесь Вы можете скачать бесплатно книги без регистрации',
            'keywords' => 'скачать книги, рецензии, отзывы на книги, цитаты из книг, краткое содержание, без регистрации, топбук',
            'og' => [
                'og:site_name' => 'TopBook.com.ua - электронная библиотека',
                'og:type' => 'website',
                'og:title' => 'Стол заказов | TopBook.com.ua - скачать книги без регистрации в fb2, epub, pdf, txt форматах',
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('OrderBundle::orders_board_list.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function voteOrderAction(Request $request)
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
}