<?php
/**
 * Created by PhpStorm.
 * User: ovsiichuk
 * Date: 28.12.18
 * Time: 18:04
 */

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use OrderBundle\Entity\Order;
use ProductBundle\Entity\Product;
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

    private ProductRouterHelper $productRouterHelper;

    private HttpClientInterface $client;

    private EntityManager $entityManager;

    public function __construct(
        Router $router,
        ProductRouterHelper $productRouterHelper,
        EntityManager $entityManager,
        HttpClientInterface $client
    ) {
        $this->route = $router;
        $this->productRouterHelper = $productRouterHelper;
        $this->entityManager = $entityManager;
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
            $link = $this->productRouterHelper->getProductPath($product, true);
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
            $link = $this->productRouterHelper->getProductPath($product, true);
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

    public function sendProductToChannel(Product $product)
    {
        $html = "<b>" . $product->translate('uk')->getName() . "</b>" . PHP_EOL;
        if ($product->getSize()) {
            $html .= "<b>Розмір:</b> " . $product->getSize() . PHP_EOL;
        }
        if ($product->getDiscount()) {
            $html .= "<b>Ціна:</b> " . "<s>" .$product->getPrice() . ' грн </s>'  . PHP_EOL;
            $html .= "<b>Ціна зі знижкою:</b> " . $product->getDiscount() . ' грн'  . PHP_EOL;
        } else {
            $html .= "<b>Ціна:</b> " . $product->getPrice() . ' грн'  . PHP_EOL;
        }

        $domain = $this->container->getParameter('full_domain');
        $link = $domain . $this->productRouterHelper->getCategoryPath($product->getCategory());
        $category = sprintf("<a href='%s'>%s</a>", $link, $product->getCategory()->translate('uk')->getName());
        $html .= "<b>Категорія:</b> " . $category . PHP_EOL;

        $this->sendTelegramPhoto("sendPhoto", [
            'chat_id' => $this->container->getParameter('telegram_channel_chat_id'),
            'product' => $product,
            'caption' => urldecode($html),
            'photo' => $product->getImage(),
            'parse_mode' => 'html',
            'has_spoiler' => true,
            'disable_web_page_preview' => true,
        ]);

        return true;
    }

    public function editPhotoToChannel(Product $product)
    {
        $html = "<b>" . $product->translate('uk')->getName() . "</b>" . PHP_EOL;
        if ($product->getSize()) {
            $html .= "<b>Розмір:</b> " . $product->getSize() . PHP_EOL;
        }
        if ($product->getDiscount()) {
            $html .= "<b>Ціна:</b> " . "<s>" .$product->getPrice() . ' грн </s>'  . PHP_EOL;
            $html .= "<b>Ціна зі знижкою:</b> " . $product->getDiscount() . ' грн'  . PHP_EOL;
        } else {
            $html .= "<b>Ціна:</b> " . $product->getPrice() . ' грн'  . PHP_EOL;
        }

        $domain = $this->container->getParameter('full_domain');
        $link = $domain . $this->productRouterHelper->getCategoryPath($product->getCategory());
        $category = sprintf("<a href='%s'>%s</a>", $link, $product->getCategory()->translate('uk')->getName());
        $html .= "<b>Категорія:</b> " . $category . PHP_EOL;

        $this->sendTelegramPhoto("editMessageMedia", [
            'chat_id' => $this->container->getParameter('telegram_channel_chat_id'),
            'product' => $product,
            'media' => $product->getImage(),
            'edit_caption' => urldecode($html),
            'message_id' => $product->getTelegramMessageId(),
            'parse_mode' => 'html',
            'has_spoiler' => true,
            'disable_web_page_preview' => true,
        ]);

        return true;
    }

//    public function editCaptionPhotoToChannel(Product $product)
//    {
//        $html = "<b>" . $product->translate('uk')->getName() . "</b>" . PHP_EOL;
//        if ($product->getDiscount()) {
//            $html .= "<b>Ціна:</b> " . "<s>" .$product->getPrice() . ' грн </s>'  . PHP_EOL;
//            $html .= "<b>Ціна зі скидкою:</b> " . $product->getDiscount() . ' грн'  . PHP_EOL;
//        } else {
//            $html .= "<b>Ціна:</b> " . $product->getPrice() . ' грн'  . PHP_EOL;
//        }
//        $link = $this->productRouterHelper->getCategoryPath($product->getCategory(), true);
//        $category = sprintf("<a href='%s'>%s</a>", $link, $product->getCategory()->translate('uk')->getName());
//        $html .= "<b>Категорія:</b> " . $category . PHP_EOL;
//
//        $this->sendTelegramPhoto("editMessageCaption", [
//            'chat_id' => $this->container->getParameter('telegram_channel_chat_id'),
//            'product' => $product,
//            'caption' => urldecode($html),
//            'message_id' => $product->getTelegramMessageId(),
//            'parse_mode' => 'html',
//            'disable_web_page_preview' => true,
//        ]);
//
//        return true;
//    }


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

    public function sendTelegramPhoto($method, $params = [])
    {
        $telegramUrlApi = $this->container->getParameter('telegram_api_url') . $this->container->getParameter('telegram_token') . '/' . $method;
        $domain = $this->container->getParameter('full_domain');

        /** @var Product $product */
        $product = $params['product'];

        $link = $domain . $this->productRouterHelper->getProductPath($product);

        $keyboard['inline_keyboard'] = [
            [
                ['text'=> 'На сайті', 'url' => sprintf('%s%s', $link, '?source=telegram_channel')],
                ['text'=> 'Instagram', 'url' => 'https://www.instagram.com/naturalstones.jewerly/']
            ]
        ];

        $arrayQuery = [
            'chat_id' => $params['chat_id'],
            'reply_markup' => json_encode($keyboard),
            'parse_mode' => 'html',
            'disable_web_page_preview' => true,
        ];

        if (isset($params['media'])) {
            $photoObject = $params['media'];

            $photo = [
                'type'=> 'photo',
                'media' => $domain . $photoObject->getPath(),
                'caption' => $params['edit_caption'],
                'parse_mode' => 'html'
            ];

            $arrayQuery['media'] = json_encode($photo);

        }

        if (isset($params['photo'])) {
            $photoObject = $params['photo'];
            $arrayQuery['photo'] = $domain . $photoObject->getPath();
//            $arrayQuery['photo'] = fopen($domain . $photoObject->getPath(), 'r');
        }

        if (isset($params['caption'])) {
            $arrayQuery['caption'] = $params['caption'];
        }

        if (isset($params['message_id'])) {
            $arrayQuery['message_id'] = $params['message_id'];
        }

        $ch = curl_init($telegramUrlApi);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayQuery);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($res, true);

        if (isset($result['ok']) && $result['ok'] == true) {

            $messageId = $result['result']['message_id'];

            $product->setTelegramMessageId($messageId);

            $this->entityManager->persist($product);
            $this->entityManager->flush();
        }
    }
}