<?php

namespace AppBundle\Block;

use AppBundle\Services\SendTelegramService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class FeedbackBlockService extends AbstractEditableBlockService
{
    public const DEFAULT_TEMPLATE = '@App/Block/feedback.html.twig';
    private const HONEYPOT_MIN_SECONDS = 3;

    public function __construct(
        Environment $twig,
        private readonly RequestStack $request,
        private readonly SendTelegramService $sendTelegramService,
    ) {
        parent::__construct($twig);
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'list_type' => null,
            'template' => self::DEFAULT_TEMPLATE,
        ]);
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $block = $blockContext->getBlock();
        if (!$block->getEnabled()) {
            return new Response();
        }

        $request = $this->request->getCurrentRequest();

        if ($request->isXmlHttpRequest()) {
            $formTime = (int) $request->get('form_time', 0);
            $honeypot = $request->get('website', '');

            if ('' !== $honeypot || (time() - $formTime) < self::HONEYPOT_MIN_SECONDS) {
                return new JsonResponse(['type' => 'success']);
            }

            $this->sendTelegramService->sendFeedback($request);

            return new JsonResponse(['type' => 'success']);
        }

        $template = $blockContext->getSetting('list_type') ?? $blockContext->getTemplate();

        return $this->renderResponse($template, [
            'block' => $block,
            'settings' => array_merge($blockContext->getSettings(), $block->getSettings()),
        ]);
    }
}