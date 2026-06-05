<?php

namespace AppBundle\EventSubscriber;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExceptionListener
{
    public function __construct(
        private readonly string $defaultLocale,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if (!$event->getThrowable() instanceof NotFoundHttpException) {
            return;
        }

        $request = $event->getRequest();

        preg_match("/^\/(uk|ru)(\/|$)/", $request->getRequestUri(), $matches);
        $locale = $matches[1] ?? $this->defaultLocale;

        if ($locale === $request->getLocale()) {
            return;
        }

        $this->translator->setLocale($locale);
        $request->setLocale($locale);
        $request->attributes->set('_locale', $locale);

        $session = $request->getSession();
        if ($session) {
            $session->set('_locale', $locale);
        }
    }
}