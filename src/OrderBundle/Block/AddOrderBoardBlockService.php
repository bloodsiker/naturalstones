<?php

namespace OrderBundle\Block;

use Doctrine\ORM\EntityManager;
use OrderBundle\Entity\OrderBoard;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Twig\Environment;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AddOrderBoardBlockService
 */
class AddOrderBoardBlockService extends AbstractBlockService
{
    const FORM_TEMPLATE = 'OrderBundle:Block:order_board_form.html.twig';
    const AJAX_ORDER_TEMPLATE = 'OrderBundle:Block:ajax_order_board.html.twig';

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * ListGenreBlockService constructor.
     *
     * @param string          $name
     * @param EngineInterface $templating
     * @param EntityManager   $em
     * @param RequestStack    $request
     */
    public function __construct(Environment $twig, EntityManager $em, RequestStack $request)
    {
        parent::__construct($twig);

        $this->em = $em;
        $this->request = $request;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver): void    {
        $resolver->setDefaults([
            'template' => self::FORM_TEMPLATE,
        ]);
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null         $response
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response    {
        $block = $blockContext->getBlock();

        if (!$block->getEnabled()) {
            return new Response();
        }

        $request = $this->request->getCurrentRequest();

        if ($request->isXmlHttpRequest() && $request->getMethod() === 'POST') {
            $order = new OrderBoard();
            $order->setUserName($request->get('name'));
            $order->setBookTitle($request->get('book'));

            $this->em->persist($order);
            $this->em->flush();
        }


        return $this->renderResponse($request->isXmlHttpRequest() ? self::AJAX_ORDER_TEMPLATE : $blockContext->getTemplate(), [
            'order'     => $order ?? null,
            'block'     => $block,
            'settings'  => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}
