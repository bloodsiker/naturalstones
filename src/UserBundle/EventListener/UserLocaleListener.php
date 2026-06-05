<?php

namespace UserBundle\EventListener;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class UserLocaleListener
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        if ($user->getLocale() !== null) {
            $locale = explode('_', $user->getLocale())[0];
            $this->requestStack->getSession()->set('_locale', $locale);
        }
    }
}
