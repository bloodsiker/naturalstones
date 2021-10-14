<?php

namespace AppBundle\Block;

use AppBundle\Services\Cart;
use Doctrine\ORM\EntityManager;
use GameBundle\Entity\Game;
use ProductBundle\Entity\Product;
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

    const TEMPLATE_BUTTON_HEAD = 'AppBundle:Block/cart:button_header.html.twig';
    const TEMPLATE_BUTTON_IN_ITEM  = 'AppBundle:Block/cart:button_in_item.html.twig';
    const TEMPLATE_BUTTON_IN_DISCOUNT_PACK  = 'AppBundle:Block/cart:button_in_discount_pack.html.twig';
    const TEMPLATE_CART_MODAL  = 'AppBundle:Block/cart:modal_cart.html.twig';
    const TEMPLATE_MODAL_PRICE_IN_SERVER  = 'AppBundle:Block/cart:modal_price_in_server.html.twig';

    const ACTION_ADD = 'add.cart';
    const ACTION_REMOVE = 'remove.cart';
    const ACTION_CLEAR = 'clear.cart';
    const ACTION_SHOW = 'show.cart';
    const ACTION_CART_RECALCULATE = 'recalculate.cart';
    const ACTION_MODAL_PRICE_IN_SERVER = 'show.modal.price';

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
            'class'       => null,
            'template'    => self::TEMPLATE_CART_MODAL,
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

        $type = $request->get('product_type');
        $action = $request->get('action');
        $item = $request->get('item_id');
        $count = $request->get('quantity');
        $game = $request->get('game_id');

        if ($request->isXmlHttpRequest() && $action) {

            switch ($action) {
                case self::ACTION_ADD:
                    $countItems = $this->addToCart($type, $item, $count);
                    return new JsonResponse(['code' => 200, 'count' => $countItems]);
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
                case self::ACTION_MODAL_PRICE_IN_SERVER:
                    $productRepository = $this->entityManager->getRepository(Product::class);
                    $item = $productRepository->find($item);
                    $priceInServer = $this->getProductPriceInServers($item, $game);
                    $blockContext->setSetting('template', self::TEMPLATE_MODAL_PRICE_IN_SERVER);
                    break;
                default:
                    throw new \Exception('Undefined action');
            }
        }

        return $this->renderResponse($blockContext->getTemplate(), [
            'countItems'    => $this->cart->countItems(),
            'cart'          => $this->cart,
            'products'      => $products ?? [],
            'priceInServer' => $priceInServer ?? [],
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
     * @param int    $id
     * @param int    $count
     *
     * @return mixed
     *
     * @throws \Exception
     */
    private function addToCart($type, int $id, int $count)
    {
        switch ($type) {
            case Cart::TYPE_PRODUCT:
                $countItem = $this->cart->addProductToCart(Cart::TYPE_PRODUCT, $id, $count);
                break;
            case Cart::TYPE_DISCOUNT:
                $countItem = $this->cart->addProductToCart(Cart::TYPE_DISCOUNT, $id, $count);
                break;
            default:
                throw new \Exception('Undefined type product');
        }

        return $countItem;
    }

    /**
     * @param string $type
     * @param int    $id
     * @param int    $count
     *
     * @return bool
     *
     * @throws \Exception
     */
    private function recalculateCart($type, int $id, int $count)
    {
        switch ($type) {
            case Cart::TYPE_PRODUCT:
                $countItem = $this->cart->recalculateCart(Cart::TYPE_PRODUCT, $id, $count);
                break;
            case Cart::TYPE_DISCOUNT:
                $countItem = $this->cart->recalculateCart(Cart::TYPE_DISCOUNT, $id, $count);
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
     * @param $item
     * @param $game
     *
     * @return mixed
     */
    private function getProductPriceInServers($item, $game)
    {
        $productRepository = $this->entityManager->getRepository(Product::class);
        $gameRepository = $this->entityManager->getRepository(Game::class);

        $game = $gameRepository->find($game);

        $qb = $productRepository->baseProductQueryBuilder();
        $productRepository->filterByGame($qb, $game);

        return $qb->andWhere('p.item = :item')->setParameter('item', $item)
            ->getQuery()
            ->getResult();
    }

    /**
     * Remove product from cart
     *
     * @param string $type
     * @param int $id
     *
     * @return bool
     */
    private function removeProductFromCart($type, int $id)
    {
        $this->cart->deleteProduct($type, $id);

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
