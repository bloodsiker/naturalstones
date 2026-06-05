<?php

namespace PageBundle\Listener;

use PageBundle\Route\RedirectRouter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class RedirectListener
{
    public function __construct(
        private readonly RedirectRouter $redirectRouter,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (false !== stripos($request->getUri(), 'admin')) {
            return;
        }

        if ('index' === $request->attributes->get('_route')) {
            return;
        }

        $urlRedirect = $this->redirectRouter->getUrlRedirect($request);
        if (null === $urlRedirect) {
            return;
        }

        $event->setResponse(new RedirectResponse($urlRedirect, Response::HTTP_MOVED_PERMANENTLY));
    }
}