<?php

namespace AppBundle\Block;

use AppBundle\Services\Cart;
use Doctrine\ORM\EntityManager;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CartBlockService
 */
class CartBlockService extends AbstractAdminBlockService
{
    const TEMPLATE_CART = 'AppBundle:Block/cart:cart.html.twig';
    const TEMPLATE_CART_QUANTITY = 'AppBundle:Block/cart:cart_quantity.html.twig';
    const TEMPLATE_CART_PAGE = 'AppBundle:Block/cart:cart_page.html.twig';
    const TEMPLATE_CART_STEP_1_PAGE = 'AppBundle:Block/cart:cart_step_1_page.html.twig';

    const TEMPLATE_BUTTON_HEAD = 'AppBundle:Block/cart:button_header.html.twig';
    const TEMPLATE_BUTTON_IN_PRODUCT  = 'AppBundle:Block/cart:product_button.html.twig';
    const TEMPLATE_BUTTON_CLEAR  = 'AppBundle:Block/cart:cart_clear_button.html.twig';

    const ACTION_ADD = 'add.cart';
    const ACTION_REMOVE = 'remove.cart';
    const ACTION_CLEAR = 'clear.cart';
    const ACTION_SHOW = 'show.cart';
    const ACTION_CART_RECALCULATE = 'recalculate.cart';

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * CartBlockService constructor.
     *
     * @param string          $name
     * @param EngineInterface $templating
     * @param Cart            $cart
     * @param RequestStack    $request
     * @param EntityManager   $entityManager
     */
    public function __construct($name, EngineInterface $templating, Cart $cart, RequestStack $request, EntityManager $entityManager)
    {
        parent::__construct($name, $templating);

        $this->cart = $cart;
        $this->request  = $request;
        $this->entityManager = $entityManager;
    }

    /**
     * @param null $code
     *
     * @return Metadata
     */
    public function getBlockMetadata($code = null)
    {
        return new Metadata(
            $this->getName(),
            (!is_null($code) ? $code : $this->getName()),
            false,
            'AppBundle',
            ['class' => 'fa fa-th-large']
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'product'     => null,
            'action'      => null,
            'class'       => null,
            'template'    => self::TEMPLATE_CART,
        ]);
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null $response
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        if (!$blockContext->getBlock()->getEnabled()) {
            return new Response();
        }

        $request = $this->request->getCurrentRequest();

        $type = Cart::TYPE_PRODUCT;
        $action = $request->get('action');
        $item = $request->get('item_id');
        $count = $request->get('quantity') ?: 1;
        $colour = $request->get('colour_id');
        $template = $request->get('template');

        if ($blockContext->getSetting('action') === self::ACTION_SHOW) {
            $products = $this->getProductInfoFromCart();
        }

        if ($template) {
            $blockContext->setSetting('template', $template);
        }

        if ($request->isXmlHttpRequest() && $action) {

            switch ($action) {
                case self::ACTION_ADD:
                    $countItems = $this->addToCart($type, $item, $count, $colour);
                    return new JsonResponse(['code' => 200, 'count' => $countItems, 'total' => $this->cart->getTotalPrice()]);
                    break;
                case self::ACTION_REMOVE:
                    $this->removeProductFromCart($type, $item);
                    $products = $this->getProductInfoFromCart();
                    break;
                case self::ACTION_CLEAR:
                    $this->clearCart();
                    break;
                case self::ACTION_SHOW:
                    $products = $this->getProductInfoFromCart();
                    break;
                case self::ACTION_CART_RECALCULATE:
                    $this->recalculateCart($type, $item, $count);
                    $products = $this->getProductInfoFromCart();
                    break;
                default:
                    throw new \Exception('Undefined action');
            }
        }

        return $this->renderResponse($blockContext->getTemplate(), [
            'countItems'    => $this->cart->countItems(),
            'cart'          => $this->cart,
            'products'      => $products ?? [],
            'count'         => $count,
            'item'          => $item,
            'settings'      => $blockContext->getSettings(),
            'block'         => $blockContext->getBlock(),
        ]);
    }

    /**
     * Add product to cart
     *
     * @param string $type
     * @param string $id
     * @param int    $count
     * @param int|null $colour
     *
     * @return mixed
     *
     * @throws \Exception
     */
    private function addToCart($type, string $id, int $count, $colour = null)
    {
        switch ($type) {
            case Cart::TYPE_PRODUCT:
                $countItem = $this->cart->addProductToCart(Cart::TYPE_PRODUCT, $id, $count, $colour);
                break;
            default:
                throw new \Exception('Undefined type product');
        }

        return $countItem;
    }

    /**
     * @param string $type
     * @param string $key
     * @param int    $count
     *
     * @return bool
     *
     * @throws \Exception
     */
    private function recalculateCart($type, string $key, int $count)
    {
        switch ($type) {
            case Cart::TYPE_PRODUCT:
                $countItem = $this->cart->recalculateCart(Cart::TYPE_PRODUCT, $key, $count);
                break;
            default:
                throw new \Exception('Undefined type product');
        }

        return $countItem;
    }

    /**
     * @return array
     */
    private function getProductInfoFromCart()
    {
        return $this->cart->getProductsInfo();
    }

    /**
     * Remove product from cart
     *
     * @param string $type
     * @param string $id
     *
     * @return bool
     */
    private function removeProductFromCart($type, string $key)
    {
        $this->cart->deleteProduct($type, $key);

        return true;
    }

    /**
     * Clear cart
     *
     * @return bool
     */
    private function clearCart()
    {
        $this->cart->clear();

        return true;
    }
}
