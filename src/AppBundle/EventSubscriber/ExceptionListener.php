<?php
/**
 * Created by PhpStorm.
 * User: pretorian42
 * Date: 10.09.18
 * Time: 17:47
 */

namespace AppBundle\EventSubscriber;



use Sonata\PageBundle\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ExceptionListener
 */

class ExceptionListener extends \Sonata\PageBundle\Listener\ExceptionListener
{
    private $defaultLocale;
    private $translator;
    /**
     * Handles a kernel exception.
     *
     * @param GetResponseForExceptionEvent $event
     *
     * @throws \Exception
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ($event->getException() instanceof NotFoundHttpException && $this->cmsManagerSelector->isEditor()) {
            $pathInfo = $event->getRequest()->getPathInfo();

            // can only create a CMS page, so the '_route' must be null
            $creatable = !$event->getRequest()->get('_route') && $this->decoratorStrategy->isRouteUriDecorable($pathInfo);

            if ($creatable) {
                $response = new Response($this->templating->render('@SonataPage/Page/create.html.twig', [
                    'pathInfo' => $pathInfo,
                    'site' => $this->siteSelector->retrieve(),
                    'creatable' => $creatable,
                ]), 404);

                $event->setResponse($response);
                $event->stopPropagation();

                return;
            }
        }

        if ($event->getException() instanceof InternalErrorException) {
            $this->handleInternalError($event);
        } else {
            $this->handleNativeError($event);
        }
    }


    /**
     * @param mixed $defaultLocale
     */
    public function setDefaultLocale($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @param mixed $translator
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Handles an internal error.
     *
     * @param GetResponseForExceptionEvent $event
     */
    private function handleInternalError(GetResponseForExceptionEvent $event)
    {
        $content = $this->templating->render('@SonataPage/internal_error.html.twig', [
            'exception' => $event->getException(),
        ]);

        $event->setResponse(new Response($content, 500));
    }

    /**
     * Handles a native error.
     *
     * @param GetResponseForExceptionEvent $event
     *
     * @throws mixed
     */
    private function handleNativeError(GetResponseForExceptionEvent $event)
    {
        if (true === $this->debug) {
            return;
        }

        if (true === $this->status) {
            return;
        }

        $this->status = true;

        $exception = $event->getException();
        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;

        $cmsManager = $this->cmsManagerSelector->retrieve();

        if ($event->getRequest()->get('_route') && !$this->decoratorStrategy->isRouteNameDecorable($event->getRequest()->get('_route'))) {
            return;
        }

        if (!$this->decoratorStrategy->isRouteUriDecorable($event->getRequest()->getPathInfo())) {
            return;
        }

        if (!$this->hasErrorCode($statusCode)) {
            return;
        }

        $message = sprintf('%s: %s (uncaught exception) at %s line %s', get_class($exception), $exception->getMessage(), $exception->getFile(), $exception->getLine());

        $this->logException($exception, $exception, $message);

        try {
            $page = $this->getErrorCodePage($statusCode);

            $cmsManager->setCurrentPage($page);

            if ($page->getSite()->getLocale() !== $event->getRequest()->getLocale()) {
                // Compare locales because Request returns the default one if null.

                // If 404, LocaleListener from HttpKernel component of Symfony is not called.
                // It uses the "_locale" attribute set by SiteSelectorInterface to set the request locale.
                // So in order to translate messages, force here the locale with the site.
                $locale = $page->getSite()->getLocale();
                if (!$locale) {
                    preg_match("/^(?:\/?(?:app.php|app_dev.php|app_admin.php)?)\/(ua|uk|ru)[\/a-zA-Z]+$/", $event->getRequest()->getRequestUri(), $result);
                    $locale = isset($result[1]) ? $result[1] : $this->defaultLocale;
                    if ($locale) {
                        $event->getRequest()->setLocale($locale);
                        if ('ua' === $locale) {
                            $locale = 'uk';
                        }
//                        $this->getErrorCodePage(404)->getSite()->setLocale($locale);
//                        $event->getRequest()->setLocale($locale);
                    }
                }
                $this->translator->setLocale($locale);
                $event->getRequest()->setLocale($locale);
                $event->getRequest()->attributes->set('_locale', $locale);
                $event->getRequest()->getSession()->set('_locale', $locale);
            }

            $response = $this->pageServiceManager->execute($page, $event->getRequest(), [], new Response('', $statusCode));
        } catch (\Exception $e) {
            $this->logException($exception, $e);

            $event->setException($e);
            $this->handleInternalError($event);

            return;
        }

        $event->setResponse($response);
    }

    /**
     * Logs exceptions.
     *
     * @param \Exception  $originalException  Original exception that called the listener
     * @param \Exception  $generatedException Generated exception
     * @param string|null $message            Message to log
     */
    private function logException(\Exception $originalException, \Exception $generatedException, $message = null)
    {
        if (!$message) {
            $message = sprintf('Exception thrown when handling an exception (%s: %s)', get_class($generatedException), $generatedException->getMessage());
        }

        if (null !== $this->logger) {
            if (!$originalException instanceof HttpExceptionInterface || $originalException->getStatusCode() >= 500) {
                $this->logger->critical($message, ['exception' => $originalException]);
            } else {
                $this->logger->error($message, ['exception' => $originalException]);
            }
        } else {
            error_log($message);
        }
    }
}
