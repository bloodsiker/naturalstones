<?php

namespace AppBundle\Block;

use AppBundle\Services\SendTelegramService;
use BookBundle\Entity\Book;
use Doctrine\ORM\EntityManager;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

/**
 * Class FeedbackBlockService
 */
class FeedbackBlockService extends AbstractAdminBlockService
{
    const DEFAULT_TEMPLATE = 'AppBundle:Block:feedback.html.twig';

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * @var SendTelegramService
     */
    private $sendTelegramService;

    /**
     * FeedbackBlockService constructor.
     *
     * @param string          $name
     * @param EngineInterface $templating
     * @param RequestStack    $request
     */
    public function __construct($name, EngineInterface $templating, RequestStack $request, SendTelegramService $sendTelegramService)
    {
        parent::__construct($name, $templating);

        $this->request = $request;
        $this->sendTelegramService = $sendTelegramService;
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
            'list_type'   => null,
            'template'    => self::DEFAULT_TEMPLATE,
        ]);
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null         $response
     *
     * @return Response
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $block = $blockContext->getBlock();
        if (!$block->getEnabled()) {
            return new Response();
        }

        $request = $this->request->getCurrentRequest();

        if ($request->isXmlHttpRequest()) {
            $this->sendTelegramService->sendFeedback($request);

            return  new JsonResponse([
                'type' => 'success'
            ]);
        }

        $template = !is_null($blockContext->getSetting('list_type'))
            ? $blockContext->getSetting('list_type') : $blockContext->getTemplate();

        return $this->renderResponse($template, [
            'block'       => $block,
            'settings'    => array_merge($blockContext->getSettings(), $block->getSettings()),
        ]);
    }
}
