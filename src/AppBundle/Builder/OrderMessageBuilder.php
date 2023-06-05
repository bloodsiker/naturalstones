<?php

namespace AppBundle\Builder;

use OrderBundle\Entity\Order;
use ProductBundle\Helper\ProductRouterHelper;
use Symfony\Component\HttpFoundation\Request;

class OrderMessageBuilder implements BuilderMessage
{
    private string $message;

    private Order $order;

    private ProductRouterHelper $productRouterHelper;

    public function __construct(
        ProductRouterHelper $productRouterHelper
    ) {
        $this->productRouterHelper = $productRouterHelper;
    }

    public function get(): string
    {
        return $this->message;
    }

    public function getMessageFromQuickForm(): string
    {
        return $this->getTitleQuickForm()
            ->getMessenger()
            ->getCallMe()
            ->getTotalSum()
            ->getProducts()
            ->get();
    }

    public function getMessageFromCart(): string
    {
        return $this->getTitleFromCart()
            ->getName()
            ->getMessenger()
            ->getCallMe()
            ->getEmail()
            ->getAddress()
            ->getComment()
            ->getTotalSum()
            ->getProducts()
            ->get();
    }

    public function getMessageFeedback(Request $request): string
    {
        $this->message = "Не нашли то, что искали?" . PHP_EOL;
        $this->message .= "<b>Имя:</b> " . $request->get('name') . PHP_EOL;
        $this->message .= "<b>Телефон:</b> " . $request->get('phone') . PHP_EOL;
        $this->message .= "<b>Email:</b> " . $request->get('email') . PHP_EOL;
        $this->message .= "<b>Сообщение:</b> " . $request->get('message') . PHP_EOL;

        return $this->get();
    }

    public function setOrder(Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getTitleFromCart(): self
    {
        $this->message = "<b>Заказ #" . $this->order->getId() . "</b>" . PHP_EOL;
        $this->message .= "Заказ с корзины" . PHP_EOL;

        return $this;
    }

    public function getTitleQuickForm(): self
    {
        $this->message = "<b>Заказ #" . $this->order->getId() . "</b>" . PHP_EOL;
        $this->message .= "Форма быстрого заказа" . PHP_EOL;

        return $this;
    }

    public function getMessenger(): self
    {
        if ($this->order->getMessenger() === 'instagram') {
            $this->message .= "<b>Ссылка на инстаграм:</b> " . $this->order->getInstagram() . PHP_EOL;
        } else {
            $this->message .= "<b>Телефон:</b> " . $this->order->getPhone() . ' - ' . $this->order->getMessenger() . PHP_EOL;
        }

        return $this;
    }

    public function getName(): self
    {
        $this->message .= "<b>Имя:</b> " . $this->order->getFio() . PHP_EOL;

        return $this;
    }

    public function getEmail(): self
    {
        if ($this->order->getEmail()) {
            $this->message .= "<b>Email:</b> " . $this->order->getEmail() . PHP_EOL;
        }

        return $this;
    }

    public function getAddress(): self
    {
        if ($this->order->getAddress()) {
            $this->message .= "<b>Адрес:</b> " . $this->order->getAddress() . PHP_EOL;
        }

        return $this;
    }

    public function getComment(): self
    {
        if ($this->order->getComment()) {
            $this->message .= "<b>Комментарий:</b> " . $this->order->getComment() . PHP_EOL;
        }

        return $this;
    }

    public function getCallMe(): self
    {
        if ($this->order->getCallMe()) {
            $this->message .= "<b>Перезвонить:</b> Да" . PHP_EOL;
        }

        return $this;
    }

    public function getTotalSum(): self
    {
        $this->message .= "<b>Сумма заказа:</b> " . number_format($this->order->getTotalSum(), 2, ',', ' ') . ' грн' . PHP_EOL;

        return $this;
    }

    public function getProducts(): self
    {
        foreach ($this->order->getOrderHasItems() as $orderItem) {
            $product = $orderItem->getProduct();
            $link = $this->productRouterHelper->getProductPath($product, true);
            $this->message .= "--- " . sprintf("<a href='%s'>%s %s</a>", $link, $product->getName(), $product->getSize()  ) ." - " . $orderItem->getQuantity() . 'шт' . ' - ' . $orderItem->getPrice() . ' грн';
            if ($orderItem->getColour()) {
                $this->message .= " Цвет: " . $orderItem->getColour()->getName();
            }
            if ($orderItem->getOptions()) {
                $this->message .= " Параметры : " . $orderItem->getOptions();
            }
            if ($orderItem->getDiscount()) {
                $this->message .= ' (Скидка ' . $orderItem->getDiscount() . ' грн)' . PHP_EOL;
            } else {
                $this->message .= PHP_EOL;
            }
        }

        return $this;
    }
}