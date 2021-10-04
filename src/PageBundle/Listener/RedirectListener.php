<?php

namespace PageBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use PageBundle\Entity\PageRedirect;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use PageBundle\Route\RedirectRouter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RedirectListener
 */
class RedirectListener
{
    /**
     * @var RedirectRouter
     */
    private $redirectRouter;

    /**
     * RedirectListener constructor.
     *
     * @param RedirectRouter $redirectRouter
     */
    public function __construct(RedirectRouter $redirectRouter)
    {
        $this->redirectRouter = $redirectRouter;
    }

    /**
     * @param GetResponseEvent $event
     *
     * @throws \Exception
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $isMain = 'index' === $request->attributes->get('_route');

        if (false === stripos($request->getUri(), 'admin')) {
            $urlRedirect = $this->redirectRouter->getUrlRedirect($request);

            if (!$isMain && null !== $urlRedirect) {
                $response = new RedirectResponse($urlRedirect, Response::HTTP_MOVED_PERMANENTLY);
                $event->setResponse($response);
            }
        }
    }
}
