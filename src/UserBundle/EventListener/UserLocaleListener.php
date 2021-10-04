<?php

namespace UserBundle\EventListener;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Setting the locale based on user's preferences.
 * @package UserBundle\EventListener
 */
class UserLocaleListener
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        if ($user->getLocale() !== null) {
            $locale = explode('_', $user->getLocale())[0];
            $this->session->set('_locale', $locale);
        }
    }
}
