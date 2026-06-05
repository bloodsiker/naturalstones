<?php

namespace PageBundle\Route;

use PageBundle\Entity\PageRedirect;
use PageBundle\Model\RedirectInterface;
use PageBundle\Model\RedirectManagerInterface;
use Sonata\PageBundle\Model\Site;
use Symfony\Cmf\Component\Routing\ChainedRouterInterface;
use Symfony\Cmf\Component\Routing\VersatileGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

class RedirectRouter implements ChainedRouterInterface
{
    protected RequestContext $context;

    private ?Request $request = null;

    public function __construct(
        protected readonly RedirectManagerInterface $redirectManager,
        protected readonly RouterInterface $router,
    ) {
        $this->context = $this->router->getContext();
    }

    public function supports($name): bool
    {
        if (is_string($name)) {
            return false;
        }

        return !is_object($name) || $name instanceof RedirectInterface;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getRouteDebugMessage(string $name, array $parameters = []): string
    {
        if ($this->router instanceof VersatileGeneratorInterface) {
            return $this->router->getRouteDebugMessage($name, $parameters);
        }

        return "Route '$name' not found";
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        throw new RouteNotFoundException('Implement generate() method');
    }

    public function setContext(RequestContext $context): void
    {
        $this->context = $context;
    }

    public function getContext(): RequestContext
    {
        return $this->context;
    }

    public function getRouteCollection(): RouteCollection
    {
        return new RouteCollection();
    }

    public function getUrlRedirect(Request $request): ?string
    {
        $this->request = $request;

        try {
            return $this->match($request->getPathInfo())['path'] ?? null;
        } catch (ResourceNotFoundException) {
            return null;
        }
    }

    /**
     * @return array{_controller: string, path: string, permanent: true}
     *
     * @throws \Exception
     */
    public function match(string $pathinfo): array
    {
        if (false !== stripos($pathinfo, '_profiler')) {
            throw new ResourceNotFoundException();
        }

        $url = null;
        foreach ($this->getActualRedirects() as $redirect) {
            $url = $this->matchRedirect($redirect, $pathinfo) ?? $url;
        }

        if (null === $url) {
            throw new ResourceNotFoundException();
        }

        return [
            '_controller' => 'Symfony\Component\HttpKernel\Controller\RedirectController::urlRedirectAction',
            'path' => $url,
            'permanent' => true,
        ];
    }

    /**
     * @return list<PageRedirect>
     */
    public function getActualRedirects(): array
    {
        $listRedirect = $this->redirectManager->findBy(['isActive' => true]);
        usort(
            $listRedirect,
            static fn ($a, $b) => mb_strlen($a->getFromPath()) <=> mb_strlen($b->getFromPath())
        );

        return array_reverse($listRedirect);
    }

    /**
     * @throws \Exception
     */
    private function matchRedirect(PageRedirect $redirect, string $pathinfo): ?string
    {
        $fromPath = $redirect->getFromPath();
        $checkPath = str_starts_with($fromPath, 'https://') ? $this->request->getUri() : $this->request->getPathInfo();

        return match ($redirect->getType()) {
            PageRedirect::TYPE_FULL => $fromPath === $checkPath ? $this->getFinalUrl($redirect) : null,
            PageRedirect::TYPE_SEGMENT => preg_match("~$fromPath~", $pathinfo) ? $this->getFinalUrl($redirect) : null,
            PageRedirect::TYPE_REGEX => preg_match($fromPath, $pathinfo) ? $this->getFinalUrl($redirect) : null,
            PageRedirect::TYPE_STARTS_WITH => str_starts_with($checkPath, $fromPath)
                ? str_replace($fromPath, $redirect->getToPath(), $checkPath)
                : null,
            default => throw new \Exception(sprintf('Unknown PageRedirect const %s', $redirect->getType())),
        };
    }

    protected function getFinalUrl(PageRedirect $objectRedirect): string
    {
        if (null !== $objectRedirect->getToPage()) {
            $toPage = $objectRedirect->getToPage();
            if ('page_slug' === $toPage->getRouteName()) {
                $url = $this->decorateUrl($toPage->getUrl(), [], self::ABSOLUTE_URL, $toPage->getSite());
            } else {
                $this->context->setHost($toPage->getSite()->getHost());
                $url = $this->router->generate($toPage->getRouteName(), [], self::ABSOLUTE_URL);
            }
        } else {
            $url = $this->decorateUrl($objectRedirect->getToPath(), [], self::ABSOLUTE_PATH);
        }

        if (null === $url) {
            throw new ResourceNotFoundException();
        }

        return $url;
    }

    /**
     * Method from Sonata Page Bundle code! Slightly modified.
     *
     * Decorates a URL with url context and query.
     *
     * @param array<string, mixed> $parameters
     *
     * @throws \RuntimeException
     */
    protected function decorateUrl(string $url, array $parameters = [], int $referenceType = self::RELATIVE_PATH, ?Site $site = null): string
    {
        if (!$this->context) {
            throw new \RuntimeException('No context associated to the CmsPageRouter');
        }

        $schemeAuthority = $this->buildSchemeAuthority($referenceType, $site);

        $url = self::RELATIVE_PATH === $referenceType
            ? UrlGenerator::getRelativePath($this->context->getPathInfo(), $url)
            : sprintf('%s%s', $schemeAuthority, $url);

        if (count($parameters) > 0) {
            return sprintf('%s?%s', $url, http_build_query($parameters, '', '&'));
        }

        return $url;
    }

    private function buildSchemeAuthority(int $referenceType, ?Site $site): string
    {
        if (!$this->context->getHost() || (self::ABSOLUTE_URL !== $referenceType && self::NETWORK_PATH !== $referenceType)) {
            return '';
        }

        $port = $this->resolvePort();
        $scheme = self::NETWORK_PATH === $referenceType ? '//' : sprintf('%s://', $this->context->getScheme());
        $host = $site instanceof Site ? $site->getHost() : $this->context->getHost();

        return $scheme . $host . $port;
    }

    private function resolvePort(): string
    {
        if ('http' === $this->context->getScheme() && 80 !== $this->context->getHttpPort()) {
            return sprintf(':%s', $this->context->getHttpPort());
        }
        if ('https' === $this->context->getScheme() && 443 !== $this->context->getHttpsPort()) {
            return sprintf(':%s', $this->context->getHttpsPort());
        }

        return '';
    }
}