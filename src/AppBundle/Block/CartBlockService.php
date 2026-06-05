<?php

namespace AppBundle\Block;

use AppBundle\Services\Cart;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class CartBlockService extends AbstractEditableBlockService
{
    public const TEMPLATE_CART = '@App/Block/cart/cart.html.twig';
    public const TEMPLATE_CART_QUANTITY = '@App/Block/cart/cart_quantity.html.twig';
    public const TEMPLATE_CART_PAGE = '@App/Block/cart/cart_page.html.twig';
    public const TEMPLATE_CART_STEP_1_PAGE = '@App/Block/cart/cart_step_1_page.html.twig';

    public const TEMPLATE_BUTTON_HEAD = '@App/Block/cart/button_header.html.twig';
    public const TEMPLATE_BUTTON_IN_PRODUCT = '@App/Block/cart/product_button.html.twig';
    public const TEMPLATE_BUTTON_CLEAR = '@App/Block/cart/cart_clear_button.html.twig';

    public const ACTION_ADD = 'add.cart';
    public const ACTION_REMOVE = 'remove.cart';
    public const ACTION_CLEAR = 'clear.cart';
    public const ACTION_SHOW = 'show.cart';
    public const ACTION_CART_RECALCULATE = 'recalculate.cart';

    public function __construct(
        Environment $twig,
        private readonly Cart $cart,
        private readonly RequestStack $request,
    ) {
        parent::__construct($twig);
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'product' => null,
            'action' => null,
            'class' => null,
            'template' => self::TEMPLATE_CART,
        ]);
    }

    /**
     * @throws \Exception
     */
    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        if (!$blockContext->getBlock()->getEnabled()) {
            return new Response();
        }

        $request = $this->request->getCurrentRequest();
        $type = Cart::TYPE_PRODUCT;
        $action = $request->get('action');
        $item = $request->get('item_id');
        $count = (int) ($request->get('quantity') ?: 1);
        $colour = $request->get('colour_id') ? (int) $request->get('colour_id') : null;
        $template = $request->get('template');

        $options = [
            'option' => $request->get('option'),
            'value' => $request->get('option_value'),
        ];

        if (self::ACTION_SHOW === $blockContext->getSetting('action')) {
            $products = $this->cart->getProductsInfo();
        }

        if ($template) {
            $blockContext->setSetting('template', $template);
        }

        if ($request->isXmlHttpRequest() && $action) {
            switch ($action) {
                case self::ACTION_ADD:
                    $countItems = $this->addToCart($type, $item, $count, $colour, $options);

                    return new JsonResponse(['code' => 200, 'count' => $countItems, 'total' => $this->cart->getTotalPrice()]);
                case self::ACTION_REMOVE:
                    $this->cart->deleteProduct($type, $item);
                    $products = $this->cart->getProductsInfo();
                    break;
                case self::ACTION_CLEAR:
                    $this->cart->clear();
                    break;
                case self::ACTION_SHOW:
                    $products = $this->cart->getProductsInfo();
                    break;
                case self::ACTION_CART_RECALCULATE:
                    $this->recalculateCart($type, $item, $count);
                    $products = $this->cart->getProductsInfo();
                    break;
                default:
                    throw new \Exception('Undefined action');
            }
        }

        return $this->renderResponse($blockContext->getTemplate(), [
            'countItems' => $this->cart->countItems(),
            'cart' => $this->cart,
            'products' => $products ?? [],
            'count' => $count,
            'item' => $item,
            'settings' => $blockContext->getSettings(),
            'block' => $blockContext->getBlock(),
        ]);
    }

    /**
     * @param array{option: string|null, value: string|int|null} $options
     *
     * @throws \Exception
     */
    private function addToCart(string $type, string $id, int $count, ?int $colour = null, array $options = []): int
    {
        if (Cart::TYPE_PRODUCT !== $type) {
            throw new \Exception('Undefined type product');
        }

        return $this->cart->addProductToCart(Cart::TYPE_PRODUCT, $id, $count, $colour, $options);
    }

    /**
     * @throws \Exception
     */
    private function recalculateCart(string $type, string $key, int $count): bool
    {
        if (Cart::TYPE_PRODUCT !== $type) {
            throw new \Exception('Undefined type product');
        }

        return $this->cart->recalculateCart(Cart::TYPE_PRODUCT, $key, $count);
    }
}