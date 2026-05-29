<?php
/**
 * Created by PhpStorm.
 * User: ovsiichuk
 * Date: 28.12.18
 * Time: 18:04
 */

namespace AppBundle\Services;

use AppBundle\Builder\BuilderMessage;
use AppBundle\Builder\OrderMessageBuilder;
use Doctrine\ORM\EntityManagerInterface;
use OrderBundle\Entity\Order;
use ProductBundle\Entity\Product;
use ProductBundle\Helper\ProductRouterHelper;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class SendTelegramService
{
    private RouterInterface $route;
    private ProductRouterHelper $productRouterHelper;
    private HttpClientInterface $client;
    private EntityManagerInterface $entityManager;
    private OrderMessageBuilder $orderMessageBuilder;
    private LoggerInterface $logger;
    private string $telegramChatId;
    private string $telegramChannelChatId;
    private string $telegramApiUrl;
    private string $telegramToken;
    private string $fullDomain;

    public function __construct(
        RouterInterface $router,
        ProductRouterHelper $productRouterHelper,
        EntityManagerInterface $entityManager,
        HttpClientInterface $client,
        BuilderMessage $orderMessageBuilder,
        LoggerInterface $logger,
        string $telegramChatId,
        string $telegramChannelChatId,
        string $telegramApiUrl,
        string $telegramToken,
        string $fullDomain
    ) {
        $this->route = $router;
        $this->productRouterHelper = $productRouterHelper;
        $this->entityManager = $entityManager;
        $this->client = $client;
        $this->orderMessageBuilder = $orderMessageBuilder;
        $this->logger = $logger;
        $this->telegramChatId = $telegramChatId;
        $this->telegramChannelChatId = $telegramChannelChatId;
        $this->telegramApiUrl = $telegramApiUrl;
        $this->telegramToken = $telegramToken;
        $this->fullDomain = $fullDomain;
    }

    public function sendMessageFromQuickForm(Order $order)
    {
        $adminOrderLink = $this->route->generate('admin_order_order_edit', ['id' => $order->getId()], Router::ABSOLUTE_URL);

        $html = $this->orderMessageBuilder
            ->setOrder($order)
            ->getMessageFromQuickForm();

        $keyboard['inline_keyboard'] = [
            [
                ['text'=> 'Заказ', 'url' => $adminOrderLink]
            ]
        ];

        $this->requestTelegram("sendMessage", [
            'chat_id' => $this->telegramChatId,
            'text' => urldecode($html),
            'parse_mode' => 'html',
            'disable_web_page_preview' => true,
            'reply_markup' => json_encode($keyboard),
        ]);
    }

    public function sendMessageFromCart(Order $order)
    {
        $adminOrderLink = $this->route->generate('admin_order_order_edit', ['id' => $order->getId()], Router::ABSOLUTE_URL);

        $html = $this->orderMessageBuilder
            ->setOrder($order)
            ->getMessageFromCart();

        $keyboard['inline_keyboard'] = [
            [
                ['text'=> 'Заказ', 'url' => $adminOrderLink]
            ]
        ];

        $this->requestTelegram("sendMessage", [
            'chat_id' => $this->telegramChatId,
            'text' => urldecode($html),
            'parse_mode' => 'html',
            'disable_web_page_preview' => true,
            'reply_markup' => json_encode($keyboard),
        ]);
    }

    public function sendFeedback(Request $request)
    {
        $html = $this->orderMessageBuilder->getMessageFeedback($request);

        $this->requestTelegram("sendMessage", [
            'chat_id' => $this->telegramChatId,
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

        $link = $this->fullDomain . $this->productRouterHelper->getCategoryPath($product->getCategory());
        $category = sprintf("<a href='%s'>%s</a>", $link, $product->getCategory()->translate('uk')->getName());
        $html .= "<b>Категорія:</b> " . $category . PHP_EOL;

        $this->sendTelegramPhoto("sendPhoto", [
            'chat_id' => $this->telegramChannelChatId,
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

        $link = $this->fullDomain . $this->productRouterHelper->getCategoryPath($product->getCategory());
        $category = sprintf("<a href='%s'>%s</a>", $link, $product->getCategory()->translate('uk')->getName());
        $html .= "<b>Категорія:</b> " . $category . PHP_EOL;

        $this->sendTelegramPhoto("editMessageMedia", [
            'chat_id' => $this->telegramChannelChatId,
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
        $telegramUrlApi = $this->telegramApiUrl . $this->telegramToken . '/';
        if (!empty($params)) {
            $url = $telegramUrlApi . $method . "?" . http_build_query($params);
        } else {
            $url = $telegramUrlApi . $method;
        }

        return $this->client->request('GET', $url);
    }

    public function sendTelegramPhoto($method, $params = [])
    {
        $telegramUrlApi = $this->telegramApiUrl . $this->telegramToken . '/' . $method;
        $domain = $this->fullDomain;

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

        $this->logger->info('Response telegram bot: ', $result ?? []);

        if (isset($result['ok']) && $result['ok'] == true) {

            $messageId = $result['result']['message_id'];

            $product->setTelegramMessageId($messageId);

            $this->entityManager->persist($product);
            $this->entityManager->flush();
        }
    }
}