<?php
/**
 * Created by PhpStorm.
 * User: ovsiichuk
 * Date: 28.12.18
 * Time: 18:04
 */

namespace AppBundle\Services;

use OrderBundle\Entity\Order;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Routing\Router;

/**
 * Class SendTelegramService
 */
class SendTelegramService
{
    use ContainerAwareTrait;

    private $route;

    /**
     * SendTelegramService constructor.
     */
    public function __construct(Router $router)
    {
        $this->route = $router;
    }

    public function sendMessageFromQuickForm(Order $order)
    {
        $adminOrderLink = $this->route->generate('admin_order_order_edit', ['id' => $order->getId()], Router::ABSOLUTE_URL);

        $html = "<b>Заказ #" . $order->getId() . "</b>" . PHP_EOL;
        $html .= "Форма быстрого заказа" . PHP_EOL;
        $html .= "<b>Телефон:</b> " . $order->getPhone() . ' - ' . $order->getMessenger() . PHP_EOL;
        $html .= "<b>Сумма заказа:</b> " . number_format($order->getTotalSum(), 2, ',', ' ') . ' грн' . PHP_EOL;
        foreach ($order->getOrderHasItems() as $orderItem) {
            $product = $orderItem->getProduct();
            $html .= "--- <b>" . $product->getName() . ' ' . $product->getSize() . "</b> - " . $orderItem->getQuantity() . 'шт' . ' - ' . $orderItem->getPrice() . ' грн';
            if ($orderItem->getDiscount()) {
                $html .= ' (Скидка ' . $orderItem->getDiscount() . ' грн)' . PHP_EOL;
            } else {
                $html .= PHP_EOL;
            }
        }
        $html .= sprintf("<a href='%s'>Ссылка на заказ</a>", $adminOrderLink);

        $this->requestTelegram("sendMessage", [
            'chat_id' => $this->container->getParameter('telegram_chat_id'),
            'text' => urldecode($html),
            'parse_mode' => 'html',
            'disable_web_page_preview' => true,
        ]);
    }

    public function sendMessageFromCart(Order $order)
    {
        $adminOrderLink = $this->route->generate('admin_order_order_edit', ['id' => $order->getId()], Router::ABSOLUTE_URL);

        $html = "<b>Заказ #" . $order->getId() . "</b>" . PHP_EOL;
        $html .= "Заказ с корзины" . PHP_EOL;
        $html .= "<b>Имя:</b> " . $order->getFio() . PHP_EOL;
        $html .= "<b>Телефон:</b> " . $order->getPhone() . ' - ' . $order->getMessenger() . PHP_EOL;
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
            $html .= "--- <b>" . $product->getName() . ' ' . $product->getSize() . "</b> - " . $orderItem->getQuantity() . 'шт' . ' - ' . $orderItem->getPrice() . ' грн';
            if ($orderItem->getDiscount()) {
                $html .= ' (Скидка ' . $orderItem->getDiscount() . ' грн)' . PHP_EOL;
            } else {
                $html .= PHP_EOL;
            }
        }
        $html .= sprintf("<a href='%s'>Ссылка на заказ</a>", $adminOrderLink);

        $this->requestTelegram("sendMessage", [
            'chat_id' => $this->container->getParameter('telegram_chat_id'),
            'text' => urldecode($html),
            'parse_mode' => 'html',
            'disable_web_page_preview' => true,
        ]);
    }


    private function requestTelegram($method, $params = [])
    {
        $telegramUrlApi = $this->container->getParameter('telegram_api_url') . $this->container->getParameter('telegram_token') . '/';
        if (!empty($params)) {
            $url = $telegramUrlApi . $method . "?" . http_build_query($params);
        } else {
            $url = $telegramUrlApi . $method;
        }

        return file_get_contents($url);
    }
}