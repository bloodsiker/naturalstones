<?php

namespace AppBundle\EventSubscriber;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Cmf\Component\Routing\ChainRouter as Router;

/**
 * Class LocaleSubscriber
 */
class LocaleSubscriber implements EventSubscriberInterface
{
    private $defaultLocale;
    private $router;

    /**
     * LocaleSubscriber constructor.
     * @param Router $router
     * @param string $defaultLocale
     */
    public function __construct(Router $router, $defaultLocale = 'en')
    {
        $this->router = $router;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        );
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $request->setDefaultLocale($this->defaultLocale);

        $this->setLocale($request);
        $this->setRouterContext($request);
    }
    /**
     * @param Router $router
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @param Request $request
     */
    private function setRouterContext(Request $request)
    {
        if (null !== $this->router) {
            $this->router->getContext()->setParameter('_locale', $request->getLocale());
        }
    }

    /**
     * @param Request $request
     */
    private function setLocale(Request $request)
    {
        $locale = $request->attributes->get('_locale', $request->getLocale());
        $request->setLocale($locale === 'ua' ? 'uk' : $locale);
    }

}