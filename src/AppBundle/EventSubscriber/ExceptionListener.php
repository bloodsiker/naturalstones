<?php

namespace AppBundle\EventSubscriber;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExceptionListener
{
    private ?string $defaultLocale = null;
    private ?TranslatorInterface $translator = null;

    public function setDefaultLocale(string $defaultLocale): void
    {
        $this->defaultLocale = $defaultLocale;
    }

    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if (!($event->getThrowable() instanceof NotFoundHttpException)) {
            return;
        }

        $request = $event->getRequest();

        preg_match("/^\/(uk|ru)(\/|$)/", $request->getRequestUri(), $result);
        $locale = $result[1] ?? $this->defaultLocale;

        if ($locale && $locale !== $request->getLocale()) {
            if ($this->translator) {
                $this->translator->setLocale($locale);
            }
            $request->setLocale($locale);
            $request->attributes->set('_locale', $locale);
            $session = $request->getSession();
            if ($session) {
                $session->set('_locale', $locale);
            }
        }
    }
}