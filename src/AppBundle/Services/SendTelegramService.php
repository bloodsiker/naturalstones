<?php
/**
 * Created by PhpStorm.
 * User: ovsiichuk
 * Date: 28.12.18
 * Time: 18:04
 */

namespace AppBundle\Services;

use OrderBundle\Entity\Order;
use ProductBundle\Helper\ProductRouterHelper;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class SendTelegramService
 */
class SendTelegramService
{
    use ContainerAwareTrait;

    private $route;

    /**
     * @var ProductRouterHelper
     */
    private $productRouterHelper;

    private HttpClientInterface $client;

    /**
     * SendTelegramService constructor.
     */
    public function __construct(Router $router, ProductRouterHelper $productRouterHelper, HttpClientInterface $client)
    {
        $this->route = $router;
        $this->productRouterHelper = $productRouterHelper;
        $this->client = $client;
    }

    public function sendMessageFromQuickForm(Order $order)
    {
        $adminOrderLink = $this->route->generate('admin_order_order_edit', ['id' => $order->getId()], Router::ABSOLUTE_URL);

        $html = "<b>Заказ #" . $order->getId() . "</b>" . PHP_EOL;
        $html .= "Форма быстрого заказа" . PHP_EOL;
        if ($order->getMessenger() === 'instagram') {
            $html .= "<b>Ссылка на инстаграм:</b> " . $order->getInstagram() . PHP_EOL;
        } else {
            $html .= "<b>Телефон:</b> " . $order->getPhone() . ' - ' . $order->getMessenger() . PHP_EOL;
        }

        if ($order->getCallMe()) {
            $html .= "<b>Перезвонить:</b> Да" . PHP_EOL;
        }

        $html .= "<b>Сумма заказа:</b> " . number_format($order->getTotalSum(), 2, ',', ' ') . ' грн' . PHP_EOL;
        foreach ($order->getOrderHasItems() as $orderItem) {
            $product = $orderItem->getProduct();
            $link = $this->productRouterHelper->getGenrePath($product, true);
            $html .= "--- " . sprintf("<a href='%s'>%s %s</a>", $link, $product->getName(), $product->getSize()  ) ." - " . $orderItem->getQuantity() . 'шт' . ' - ' . $orderItem->getPrice() . ' грн';
            if ($orderItem->getColour()) {
                $html .= " Цвет: " . $orderItem->getColour()->getName();
            }
            if ($orderItem->getOptions()) {
                $html .= " Параметры : " . $orderItem->getOptions();
            }
            if ($orderItem->getDiscount()) {
                $html .= ' (Скидка ' . $orderItem->getDiscount() . ' грн)' . PHP_EOL;
            } else {
                $html .= PHP_EOL;
            }
        }

        $keyboard['inline_keyboard'] = [
            [
                ['text'=> 'Заказ', 'url' => $adminOrderLink]
            ]
        ];

        $this->requestTelegram("sendMessage", [
            'chat_id' => $this->container->getParameter('telegram_chat_id'),
            'text' => urldecode($html),
            'parse_mode' => 'html',
            'disable_web_page_preview' => true,
            'reply_markup' => json_encode($keyboard),
        ]);
    }

    public function sendMessageFromCart(Order $order)
    {
        $adminOrderLink = $this->route->generate('admin_order_order_edit', ['id' => $order->getId()], Router::ABSOLUTE_URL);

        $html = "<b>Заказ #" . $order->getId() . "</b>" . PHP_EOL;
        $html .= "Заказ с корзины" . PHP_EOL;
        $html .= "<b>Имя:</b> " . $order->getFio() . PHP_EOL;
        if ($order->getMessenger() === 'instagram') {
            $html .= "<b>Ссылка на инстаграм:</b> " . $order->getInstagram() . PHP_EOL;
        } else {
            $html .= "<b>Телефон:</b> " . $order->getPhone() . ' - ' . $order->getMessenger() . PHP_EOL;
        }
        if ($order->getCallMe()) {
            $html .= "<b>Перезвонить:</b> Да" . PHP_EOL;
        }
        if ($order->getEmail()) {
            $html .= "<b>Email:</b> " . $order->getEmail() . PHP_EOL;
        }
        if ($order->getAddress()) {
            $html .= "<b>Адрес:</b> " . $order->getAddress() . PHP_EOL;
        }
        if ($order->getComment()) {
            $html .= "<b>Комментарий:</b> " . $order->getComment() . PHP_EOL;
        }
        $html .= "<b>Сумма заказа:</b> " . number_format($order->getTotalSum(), 2, ',', ' ') . ' грн' . PHP_EOL;
        foreach ($order->getOrderHasItems() as $orderItem) {
            $product = $orderItem->getProduct();
            $link = $this->productRouterHelper->getGenrePath($product, true);
            $html .= "---" . sprintf("<a href='%s'>%s %s</a>", $link, $product->getName(), $product->getSize()  ) ." - " . $orderItem->getQuantity() . 'шт' . ' - ' . $orderItem->getPrice() . ' грн';
            if ($orderItem->getColour()) {
                $html .= " Цвет: " . $orderItem->getColour()->getName();
            }
            if ($orderItem->getOptions()) {
                $html .= " Параметры : " . $orderItem->getOptions();
            }
            if ($orderItem->getDiscount()) {
                $html .= ' (Скидка ' . $orderItem->getDiscount() . ' грн)' . PHP_EOL;
            } else {
                $html .= PHP_EOL;
            }
        }

        $keyboard['inline_keyboard'] = [
            [
                ['text'=> 'Заказ', 'url' => $adminOrderLink]
            ]
        ];

        $this->requestTelegram("sendMessage", [
            'chat_id' => $this->container->getParameter('telegram_chat_id'),
            'text' => urldecode($html),
            'parse_mode' => 'html',
            'disable_web_page_preview' => true,
            'reply_markup' => json_encode($keyboard),
        ]);
    }

    /**
     * @param  Request  $request
     */
    public function sendFeedback(Request $request)
    {
        $html = "Не нашли то, что искали?" . PHP_EOL;
        $html .= "<b>Имя:</b> " . $request->get('name') . PHP_EOL;
        $html .= "<b>Телефон:</b> " . $request->get('phone') . PHP_EOL;
        $html .= "<b>Email:</b> " . $request->get('email') . PHP_EOL;
        $html .= "<b>Сообщение:</b> " . $request->get('message') . PHP_EOL;

        $this->requestTelegram("sendMessage", [
            'chat_id' => $this->container->getParameter('telegram_chat_id'),
            'text' => urldecode($html),
            'parse_mode' => 'html',
            'disable_web_page_preview' => true,
        ]);

        return true;
    }


    private function requestTelegram($method, $params = [])
    {
        $telegramUrlApi = $this->container->getParameter('telegram_api_url') . $this->container->getParameter('telegram_token') . '/';
        if (!empty($params)) {
            $url = $telegramUrlApi . $method . "?" . http_build_query($params);
        } else {
            $url = $telegramUrlApi . $method;
        }

        return $this->client->request('GET', $url);
    }
}