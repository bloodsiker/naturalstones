<?php

namespace AppBundle\Services;

use AppBundle\Builder\BuilderMessage;
use Doctrine\ORM\EntityManagerInterface;
use OrderBundle\Entity\Order;
use ProductBundle\Entity\Product;
use ProductBundle\Helper\ProductRouterHelper;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SendTelegramService
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly ProductRouterHelper $productRouterHelper,
        private readonly EntityManagerInterface $entityManager,
        private readonly HttpClientInterface $client,
        private readonly BuilderMessage $orderMessageBuilder,
        private readonly LoggerInterface $logger,
        private readonly string $telegramChatId,
        private readonly string $telegramChannelChatId,
        private readonly string $telegramApiUrl,
        private readonly string $telegramToken,
        private readonly string $fullDomain,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function sendMessageFromQuickForm(Order $order): array
    {
        $html = $this->orderMessageBuilder->setOrder($order)->getMessageFromQuickForm();
        $response = $this->sendOrderTelegramMessage($order, $html);
        $this->storeOrderTelegramMessageData($order, $response);

        return $response;
    }

    /**
     * @return array<string, mixed>
     */
    public function sendMessageFromCart(Order $order): array
    {
        $html = $this->orderMessageBuilder->setOrder($order)->getMessageFromCart();
        $response = $this->sendOrderTelegramMessage($order, $html);
        $this->storeOrderTelegramMessageData($order, $response);

        return $response;
    }

    public function sendFeedback(Request $request): bool
    {
        $this->requestTelegram('sendMessage', [
            'chat_id' => $this->telegramChatId,
            'text' => $this->orderMessageBuilder->getMessageFeedback($request),
            'parse_mode' => 'html',
            'disable_web_page_preview' => true,
        ]);

        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function editOrderTelegramMessage(Order $order): array
    {
        $message = Order::TYPE_ORDER_QUICK === $order->getType()
            ? $this->orderMessageBuilder->setOrder($order)->getMessageFromQuickForm()
            : $this->orderMessageBuilder->setOrder($order)->getMessageFromCart();

        if (!$order->getTelegramMessageId()) {
            $response = $this->sendOrderTelegramMessage($order, $message);
            $this->storeOrderTelegramMessageData($order, $response);

            return $response;
        }

        return $this->requestTelegram('editMessageText', [
            'chat_id' => $order->getTelegramChatId() ?: $this->telegramChatId,
            'message_id' => $order->getTelegramMessageId(),
            'text' => $message,
            'parse_mode' => 'html',
            'disable_web_page_preview' => true,
            'reply_markup' => json_encode($this->buildOrderKeyboard($order)),
        ]);
    }

    public function sendProductToChannel(Product $product): bool
    {
        $this->sendTelegramPhoto('sendPhoto', [
            'chat_id' => $this->telegramChannelChatId,
            'product' => $product,
            'caption' => urldecode($this->buildProductCaption($product)),
            'photo' => $product->getImage(),
            'parse_mode' => 'html',
            'has_spoiler' => true,
            'disable_web_page_preview' => true,
        ]);

        return true;
    }

    public function editPhotoToChannel(Product $product): bool
    {
        $this->sendTelegramPhoto('editMessageMedia', [
            'chat_id' => $this->telegramChannelChatId,
            'product' => $product,
            'media' => $product->getImage(),
            'edit_caption' => urldecode($this->buildProductCaption($product)),
            'message_id' => $product->getTelegramMessageId(),
            'parse_mode' => 'html',
            'has_spoiler' => true,
            'disable_web_page_preview' => true,
        ]);

        return true;
    }

    /**
     * @param array<string, mixed> $params
     */
    public function sendTelegramPhoto(string $method, array $params = []): void
    {
        $telegramUrlApi = $this->telegramApiUrl . $this->telegramToken . '/' . $method;

        /** @var Product $product */
        $product = $params['product'];

        $link = $this->fullDomain . $this->productRouterHelper->getProductPath($product);
        $keyboard = ['inline_keyboard' => [[
            ['text' => 'На сайті', 'url' => sprintf('%s%s', $link, '?source=telegram_channel')],
            ['text' => 'Instagram', 'url' => 'https://www.instagram.com/naturalstones.jewerly/'],
        ]]];

        $arrayQuery = [
            'chat_id' => $params['chat_id'],
            'reply_markup' => json_encode($keyboard),
            'parse_mode' => 'html',
            'disable_web_page_preview' => true,
        ];

        if (isset($params['media'])) {
            $arrayQuery['media'] = json_encode([
                'type' => 'photo',
                'media' => $this->fullDomain . $params['media']->getPath(),
                'caption' => $params['edit_caption'],
                'parse_mode' => 'html',
            ]);
        }

        if (isset($params['photo'])) {
            $arrayQuery['photo'] = $this->fullDomain . $params['photo']->getPath();
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

        if (isset($result['ok']) && true === $result['ok']) {
            $product->setTelegramMessageId($result['result']['message_id']);
            $this->entityManager->persist($product);
            $this->entityManager->flush();
        }
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    private function requestTelegram(string $method, array $params = []): array
    {
        $telegramUrlApi = $this->telegramApiUrl . $this->telegramToken . '/' . $method;
        $response = $this->client->request('POST', $telegramUrlApi, ['body' => $params]);
        $result = json_decode($response->getContent(false), true);

        $this->logger->info('Response telegram bot: ', $result ?? []);

        return $result ?? [];
    }

    private function buildProductCaption(Product $product): string
    {
        $html = '<b>' . $product->translate('uk')->getName() . '</b>' . PHP_EOL;

        if ($product->getSize()) {
            $html .= '<b>Розмір:</b> ' . $product->getSize() . PHP_EOL;
        }

        if ($product->getDiscount()) {
            $html .= '<b>Ціна:</b> <s>' . $product->getPrice() . ' грн </s>' . PHP_EOL;
            $html .= '<b>Ціна зі знижкою:</b> ' . $product->getDiscount() . ' грн' . PHP_EOL;
        } else {
            $html .= '<b>Ціна:</b> ' . $product->getPrice() . ' грн' . PHP_EOL;
        }

        $link = $this->fullDomain . $this->productRouterHelper->getCategoryPath($product->getCategory());
        $category = sprintf("<a href='%s'>%s</a>", $link, $product->getCategory()->translate('uk')->getName());
        $html .= '<b>Категорія:</b> ' . $category . PHP_EOL;

        return $html;
    }

    /**
     * @return array{inline_keyboard: array<int, array<int, array<string, string>>>}
     */
    private function buildOrderKeyboard(Order $order): array
    {
        $adminOrderLink = $this->fullDomain . $this->router->generate(
            'admin_order_order_edit',
            ['id' => $order->getId()],
            UrlGeneratorInterface::ABSOLUTE_PATH
        );

        return [
            'inline_keyboard' => [[
                ['text' => 'Заказ', 'url' => $adminOrderLink],
            ]],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function sendOrderTelegramMessage(Order $order, string $message): array
    {
        return $this->requestTelegram('sendMessage', [
            'chat_id' => $this->telegramChatId,
            'text' => $message,
            'parse_mode' => 'html',
            'disable_web_page_preview' => true,
            'reply_markup' => json_encode($this->buildOrderKeyboard($order)),
        ]);
    }

    /**
     * @param array<string, mixed> $response
     */
    private function storeOrderTelegramMessageData(Order $order, array $response): void
    {
        if (!isset($response['ok']) || true !== $response['ok']) {
            return;
        }
        if (!isset($response['result']['message_id'])) {
            return;
        }

        $order->setTelegramChatId((string) ($response['result']['chat']['id'] ?? $this->telegramChatId));
        $order->setTelegramMessageId((int) $response['result']['message_id']);

        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }
}