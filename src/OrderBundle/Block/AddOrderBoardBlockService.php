<?php

namespace OrderBundle\Block;

use Doctrine\ORM\EntityManager;
use OrderBundle\Entity\OrderBoard;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Meta\Metadata;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AddOrderBoardBlockService
 */
class AddOrderBoardBlockService extends AbstractAdminBlockService
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
    public function __construct($name, EngineInterface $templating, EntityManager $em, RequestStack $request)
    {
        parent::__construct($name, $templating);

        $this->em = $em;
        $this->request = $request;
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
            'OrderBundle',
            ['class' => 'fa fa-code']
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
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
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
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
