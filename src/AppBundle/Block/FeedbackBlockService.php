<?php

namespace AppBundle\Block;

use AppBundle\Services\SendTelegramService;
use Psr\Cache\CacheItemPoolInterface;
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
    private const RATE_LIMIT_WINDOW_SECONDS = 60;
    private const RATE_LIMIT_MAX_REQUESTS = 3;
    private const RATE_LIMIT_BLOCK_SECONDS = 600;

    public function __construct(
        Environment $twig,
        private readonly RequestStack $request,
        private readonly SendTelegramService $sendTelegramService,
        private readonly CacheItemPoolInterface $cache,
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
            if (!$this->registerRequestAndCheckLimit($request->getClientIp())) {
                return new JsonResponse(['type' => 'error'], Response::HTTP_TOO_MANY_REQUESTS);
            }

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

    private function registerRequestAndCheckLimit(?string $ip): bool
    {
        if (null === $ip) {
            return true;
        }

        $blockItem = $this->cache->getItem('feedback_form_block_'.md5($ip));
        if ($blockItem->isHit()) {
            return false;
        }

        $countItem = $this->cache->getItem('feedback_form_count_'.md5($ip));
        $count = $countItem->isHit() ? (int) $countItem->get() : 0;
        ++$count;

        if ($count > self::RATE_LIMIT_MAX_REQUESTS) {
            $blockItem->set(true);
            $blockItem->expiresAfter(self::RATE_LIMIT_BLOCK_SECONDS);
            $this->cache->save($blockItem);

            return false;
        }

        $countItem->set($count);
        $countItem->expiresAfter(self::RATE_LIMIT_WINDOW_SECONDS);
        $this->cache->save($countItem);

        return true;
    }
}