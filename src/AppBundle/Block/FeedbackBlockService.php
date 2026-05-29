<?php

namespace AppBundle\Block;

use AppBundle\Services\SendTelegramService;
use BookBundle\Entity\Book;
use Doctrine\ORM\EntityManager;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

/**
 * Class FeedbackBlockService
 */
class FeedbackBlockService extends AbstractBlockService
{
    const DEFAULT_TEMPLATE = '@App/Block/feedback.html.twig';

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
    public function __construct(Environment $twig, RequestStack $request, SendTelegramService $sendTelegramService)
    {
        parent::__construct($twig);

        $this->request = $request;
        $this->sendTelegramService = $sendTelegramService;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver): void    {
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
    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response    {
        $block = $blockContext->getBlock();
        if (!$block->getEnabled()) {
            return new Response();
        }

        $request = $this->request->getCurrentRequest();

        if ($request->isXmlHttpRequest()) {
            $formTime = (int) $request->get('form_time', 0);
            $honeypot = $request->get('website', '');

            if ($honeypot !== '' || (time() - $formTime) < 3) {
                return new JsonResponse(['type' => 'success']);
            }

            $this->sendTelegramService->sendFeedback($request);

            return new JsonResponse([
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
